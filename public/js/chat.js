$(document).ready(function(){
      var tiempo = null;
      if($.cookie('name_chat')){
            ocultar=false;
        }else{
            ocultar=true;
        }
$("#chat_div").chatbox({
                                  id : "chat_div",
                                  title : "valeplaza Chat",
                                  hidden: ocultar,
                                  offset: '',
                                  messageSent: function(id,user, msg){
                                      $("#chat_div").chatbox("option", "boxManager").addMsg(user,msg);
                                      $.ajax({
                                                type: 'post',
                                                url: '../../../chat/viewcustomerchat/',
                                                data: {
                                                    name_chat: user,
                                                    message: msg,
                                                    sessId: $('#sessId').val(),
                                                    hilo: $('#Hilo').val(),
                                                    rol: 'client'
                                                },
                                                success: function(result) {
                                                        //$.cookie('coo_chat',result, { path: '/'});
                                                },
                                                error: function(err) {
                                                }
                                            });
                                  },
                                  
                                  boxClosed: function(id) {
                                            Chat.csessionchat();
                                            $("#login_div").show();
                                            //$('#barrita_chat').show();

                                  }
                              });

             
           
 Chat = {
            userReg: function() {
                if($('#name_chat').val()!=''){
                            if($.cookie('name_chat')){
                                alert("ya existe un chat iniciado")
                            }else{
                                name=$('#name_chat').val();
                                $.ajax({
                                    type: 'post',
                                    url: '../../../chat/viewcustomerchat/',
                                    data: {
                                        name_chat: name,
                                        message: 'Buenos dias '+name+', en que lo puedo ayudar?',
                                        sessId: $('#sessId').val(),
                                        hilo: $('#Hilo').val(),
                                        rol: 'admin'
                                    },
                                    success: function(result) {
                                            $.cookie('coo_chat',result, { path: '/'});
                                            
                                            clientMsgs = $.parseJSON(result);
                                                    $(clientMsgs).each(function(k, v){
                                                        $("#chat_div").chatbox("option", "user", v.name_chat);
                                                        $.cookie('name_chat',v.name_chat, { path: '/' }); 
                                                        $("#chat_div").chatbox("option", "boxManager").addMsg('operador',v.msg_chat);
                                                    });
                                                $("#chat_div").chatbox("option", "hidden", false);
                                                //$("#chat_div").chatbox("option", "boxManager").addMsg('operador','Buenos dias '+result+', en que lo puedo ayudar?');
                                                $("#login_div").hide();
                                                tiempo=setInterval(function(){
                                                            setTimeout(function(){
                                                                Chat.getMessages();
                                                            }, 1000)
                                                        }, 5000);
                                        //}
                                    }
                                });
                            }
                }else{
                    alert("Ingrese un nombre")
                }
            },
            csessionchat:function(){
                window.clearInterval(tiempo)
                $.ajax({
                    type: 'post',
                    url: '../../../chat/cerrarchat/',
                    data: {
                        hilo:$('#Hilo').val()
                    },
                    success: function() {
                        $.removeCookie("name_chat");
                        $.removeCookie("chat_est");
                        $.removeCookie("coo_chat");
                        $.cookie('chat_est', false, { path: '/', expires: -1 });
                        $.cookie('name_chat', false, { path: '/', expires: -1 });
                        $.cookie('coo_chat', false, { path: '/', expires: -1 });
                        $("#chat_div").empty();
                        
                    },
                    error: function(err) {
                    }
                });

            },
            getMessages: function() {
                //url=window.location.host+'/chat/index/getmessages/';
                //alert(url);
                $.ajax({
                    type: 'post',
                    url: '../../../chat/getmessages/',
                    data: {
                        sessId: $('#sessId').val()
                    },
                    datatype: 'json',
                    success: function(result) {
                         if(result !='nochat'){
                                if(result !='old'){
                                    $.removeCookie("coo_chat");
                                    $.cookie('coo_chat',result, { path: '/'});
                                    $('#chat_div').empty();
                                    clientMsgs = $.parseJSON(result);
                                    $(clientMsgs).each(function(k, v){
                                        if (v.rol == 'client')
                                            $("#chat_div").chatbox("option", "boxManager").addMsg(v.name_chat,v.message);

                                        else if (v.rol == 'admin')
                                            $("#chat_div").chatbox("option", "boxManager").addMsg('operador',v.message);
                                    });
                                }else{
                                    var messages=$.cookie('coo_chat')
                                    clientMsgs = $.parseJSON(messages);
                                    if($('#chat_div').is(':empty')){
                                        $(clientMsgs).each(function(k, v){
                                            //alert(v.name_chat)
                                                        if (v.rol == 'client')
                                                            
                                                            $("#chat_div").chatbox("option", "boxManager").addMsg(v.name_chat,v.message);

                                                        else if (v.rol == 'admin')
                                                            $("#chat_div").chatbox("option", "boxManager").addMsg('operador',v.message);
                                                    });
                                    }
                                                    
                                    //$.removeCookie("coo_chat");
                                }
                        }else{
                                $.removeCookie("name_chat");
                                $.removeCookie("chat_est");
                                $.removeCookie("coo_chat");
                                $.cookie('chat_est', false, { path: '/', expires: -1 });
                                $.cookie('name_chat', false, { path: '/', expires: -1 });
                                $.cookie('coo_chat', false, { path: '/', expires: -1 });
                                window.clearInterval(tiempo)
                                $(".ui-widget").empty();
                                $("#login_div").show();
                             //$("#chat_div").chatbox("option", "boxManager").addMsg('operador','cerro el chat');
                        }
                    }
                });
            }
    }
    
            $('#barrita_chat').click(function(){

                    if($('#barrita_chat').is(':hidden')){
                       
                        $('#barrita_chat').show();
                        $('#content_user').hide();
                    }else{
                        $('.chat_minimizer').css({backgroundPosition: "-96px top"});
                        $('#barrita_chat').hide();
                        $('#content_user').show();
                        
                    }
            });
            $('.chat_tittle').click(function(){

                    if($('#barrita_chat').is(':hidden')){
                        $('#barrita_chat').show();
                        $('#content_user').hide();
                         $('.chat_minimizer').css({backgroundPosition: "-80px top"});
                    }else{
                        $('#barrita_chat').hide();
                        $('#content_user').show();
                    }
            });
            $('.chat').click(function(){
                    if($('#login_div').is(':hidden')){
                        $('#login_div').show();
                        $('.chat').hide();
                    }else{
                        $('#login_div').hide();
                        $('.chat').show();
                    }

            });
            //alert($.cookie('chat_est'))
            if($.cookie('chat_est')=='minimizado'){
                $('#barrita_chat').hide();
                $("#chat_div").chatbox("toggleContent");

            }
            //$.cookie('name_chat','', { path: '/' });
            if($.cookie('name_chat')){
                $("#chat_div").chatbox("option", "user", $.cookie('name_chat'));
                 tiempo=setInterval(function(){
                            setTimeout(function(){
                                Chat.getMessages();
                            }, 3000)
                        }, 5000);   
            }else{
                  $("#chat_div").chatbox("option", "hidden", true);
            }
});