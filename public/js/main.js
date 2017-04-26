$(function(){
    //original field values
    var field_values = {
            //id        :  value
            'rasocial'  : 'razon social',
            'rubro'  : 'rubro',
            'descripcion_breve' : 'descripcion',
            'telefono'  : 'telefono',
            'web'  : 'web'
    };


    //inputfocus
    /*$('input#username').inputfocus({ value: field_values['username'] });
    $('input#password').inputfocus({ value: field_values['password'] });
    $('input#cpassword').inputfocus({ value: field_values['cpassword'] }); 
    $('input#lastname').inputfocus({ value: field_values['lastname'] });
    $('input#firstname').inputfocus({ value: field_values['firstname'] });
    $('input#email').inputfocus({ value: field_values['email'] });*/ 




    //reset progress bar
    //$('#progress').css('width','0');
    //$('#progress_text').html('0% Complete');

    //first_step
    //$('form').submit(function(){ return false; });
    /*$('#submit_first').click(function(){
        //remove classes
        $('#first_step input').removeClass('error').removeClass('valid');

        //ckeck if inputs aren't empty
        var fields = $('#first_step input[type=text], #first_step select');
        var error = 0;
        fields.each(function(){
            var value = $(this).val();
            //value.length<4
            if( value.length<1 || value==field_values[$(this).attr('id')] ) {
                $(this).addClass('error');
                $(this).effect("shake", { times:3 }, 50);
                
                error++;
            } else {
                
                $(this).addClass('valid');
            }
        });        
        
        if(!error) {*/
            $.validator.addMethod("lettersonly",function(value, element) {
                               return /^[a-zA-ZÑñ() ]+$/.test(value);
                             },"Solo se permiten letras.");
            $.validator.addMethod("numbersonly",function(value, element) {
                                    return /[0-9]+/.test(value);
                                    },"Solo se permiten numeros.");

            $.validator.addMethod('minStrict', function (value, el, param) {
                    return this.optional(el) || value.length == param;
                });
            $('#form-datos').validate({
                        rules:{
                                    
                                    descripcion_breve : {    
                                        required : true, //para validar campo vacio
                                        maxlength: 100
                                    },
                                    /*web : {    
                                        required : true //para validar campo vacio
                                    },*/
                                    nomp : {    
                                        required : true //para validar campo vacio
                                    },
                                    /*rasocial : {    
                                        required : true //para validar campo vacio
                                    },*/
                                    telefono : {    
                                        required : true, //para validar campo vacio
                                        numbersonly:true
                                    },
                                    rubro : {    
                                        required : true //para validar campo vacio
                                    },
                                    ciudad : {    
                                        required : true //para validar campo vacio
                                    },
                                    direccion : {    
                                        required : true //para validar campo vacio
                                    }/*,
                                    facebook : {    
                                        required : true //para validar campo vacio
                                    }*/
                                                                    
                      },
                        /*messages:{  
                                    
                                    descripcion_breve : {    
                                            //para validar campo vacio
                                        required    : "Falta indicar la descripcion",  //para validar formato email
                                        maxlength: "se permite maximo 100 caracteres"
                                    },
                                    web : {    
                                            //para validar campo vacio
                                        required    : "Falta indicar la direccion web"  //para validar formato email
                                    },
                                    nomp : {    
                                        required : "Falta indicar el nombre comercial" //para validar campo vacio
                                    },
                                    rasocial : {    
                                            //para validar campo vacio
                                        required    : "Falta indicar la razon social"  //para validar formato email
                                    },
                                    telefono : {    
                                            //para validar campo vacio
                                        required    : "Falta indicar el telefono",
                                        numbersonly : "Solo se permiten numeros"
                                    },
                                    rubro : {    
                                            //para validar campo vacio
                                        required    : "Falta indicar el rubro"  //para validar formato email
                                    }
                                             
                        },*/
                        errorPlacement: function(error,element) {
                            return true;
                        },
                        highlight: function(element) {
                             $(element).addClass('errorborder');
                        }, 
                        unhighlight: function(element) {
                             $(element).removeClass('errorborder');
                        },
                        submitHandler: function(form) {
                            tinyMCE.triggerSave();
            $.ajax({
                        type: 'post',
                        url: '../../../index/step1succes',
                        data: { 
                            //'rasocial': $("#rasocial").val(),
                            'rubro': $("#rubro").val(),
                            'direccion': $("#direccion").val(),
                            //'faceboook': $("#facebook").val(),
                            'descripcion_breve': $("#descripcion_breve").val(),
                            'telefono': $("#telefono").val(),
                            'ciudad': $("#ciudad").val(),
                            'nombre_comercial': $("#nomp").val()
                                },
                        beforeSend:function(objeto){
                                                        $('#bloquea').css("display",'block');
                                                    },        
                        success: function(json) {
                                    $('#bloquea').css("display",'none');
                                    if(json =='errorlogo'){
                                        
                                        bootbox.alert("Falta subir logo en su perfil") 
                                    }else{
                                         //$('#bloquea').css("display",'none');
                                         location.reload();
                                    }
                                   
                                       
                        }
                    })
                        }
            })
                   
        /*} else return false;
    });*/


    $('#submit_second').click(function(){
        //remove classes
        $('#second_step input').removeClass('error').removeClass('valid');

        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
        var fields = $('#second_step input[type=text], #first_step select');
        var error = 0;
        fields.each(function(){
            var value = $(this).val();
            //( $(this).attr('id')=='email' && !emailPattern.test(value) )
            if(value==field_values[$(this).attr('id')] ) {
                $(this).addClass('error');
                $(this).effect("shake", { times:3 }, 50);
                
                error++;
            } else {
                $(this).addClass('valid');
            }
        });

        if(!error) {
            
            /* *************/
            $('#form-locales').validate({
                        rules:{
                                    nombre_tienda : {    
                                                    required : true //para validar campo vacio
                                             },
                                    slprovincia : {    
                                                    required : true //para validar campo vacio
                                             },         
                                    dir_telefono : {    
                                        required : true //para validar campo vacio
                                    },
                                    direccion : {    
                                        required : true //para validar campo vacio
                                    },
                                    coordenada : {    
                                        required : true //para validar campo vacio
                                    }
                                                                    
                      },
                        /*messages:{
                                    nombre_tienda : {    
                                                     //para validar campo vacio
                                                   required    : "Falta indicar el nombre del local"  //para validar formato email
                                             },
                                    slprovincia : {    
                                                    required : "Falta indicar su ciudad" //para validar campo vacio
                                             },         
                                    dir_telefono : {    
                                            //para validar campo vacio
                                        required    : "Falta indicar el telefono"  //para validar formato email
                                    },
                                    direccion : {    
                                            //para validar campo vacio
                                        required    : "Falta indicar la direccion"  //para validar formato email
                                    },
                                    coordenada : {    
                                            //para validar campo vacio
                                        required    : "Falta indicar las coordenadas"  //para validar formato email
                                    }
                                             
                        },*/
                        errorPlacement: function(error,element) {
                            return true;
                        },
                        highlight: function(element) {
                             $(element).addClass('errorborder');
                        }, 
                        unhighlight: function(element) {
                             $(element).removeClass('errorborder');
                        },
                        submitHandler: function(form) {
            var arrAlcance= new Array();
                                /*if($("#delivery").is(":checked")){
                                    editable=true;
                                }else{*/
                                    editable=false;
                                //}
                                
                                /*if($("#tip_adm").is(":checked")){
                                    neoadm=true;
                                }else{*/
                                    neoadm=false;
                                //}
                                arrAlcance=null;
                               alcance=false;
                                /*$("#form-locales").find(':input').each(function() {
                                   if($(this).is(":checked")){
                                        if(this.id != 'delivery' && this.id != 'tip_adm'){
                                            alcance=true;
                                            arrDatos = new Array();
                                            arrDatos.push($("#chkMalls_"+this.value).val());
                                            arrAlcance[this.value]=arrDatos;
                                        }
                                    }
                                });*/
                                //$("#slpais").removeAttr("disabled");
                                $("#slprovincia").removeAttr("disabled");
                                $("#slciudad").removeAttr("disabled");
                                $("#delivery").removeAttr("disabled");
                                estado=false;
                                /*if($("#delivery").is(":checked")){
                                    if(alcance){
                                        estado=true;
                                    }else{
                                        
                                        estado=false;
                                    }
                                }else{
                                    if(alcance){
                                        
                                        estado=false;
                                    }else{
                                        
                                        estado=true;
                                    }
                                }*/
                                //if(estado){
                                    if(neoadm){
                                        /* se ha pedido un nuevo adm para el local */
                                        bootbox.dialog({
                                                        message: "El operador sera quien realize los canjes de los vales para este local <br>Ingresa el correo de un operador que se hara cargo de este local, posteriormente puede agregar más operadores al local<br/><br><p class='modal_tr'>Correo del operador :</p><input type='email' id='usu_email' name='usu_email'></input><br/><br><p class='modal_tr'>Contraseña:</p><input  type='password' name='usu_pass' id='usu_pass'></input><br/>",
                                                            title: "Ingresa al responsable del local",
                                                            buttons: {
                                                            main: {
                                                            label: "Guardar",
                                                            className: "btn-primary",
                                                            callback: function() {
                                                                                    if($("#usu_email").val() !='' && $("#usu_pass").val() !=''){
                                                                                    form=document.getElementById('form-locales')
                                                                                            $.ajax({
                                                                                                        type: 'post',
                                                                                                        url: $(form).attr('action'),
                                                                                            data: {
                                                                                                'datos':$(form).serialize(),      
                                                                                                'alcance':arrAlcance,
                                                                                                'tip':$("#h_local").val(),
                                                                                                'editable':editable,
                                                                                                'usu_email':$("#usu_email").val(),
                                                                                                'usu_pass': $("#usu_pass").val()
                                                                                                        },
                                                                                                beforeSend:function(objeto){
                                                                                                                                $('#bloquea').css("display",'block');
                                                                                                                            },        
                                                                                                success: function(json) {
                                                                                                    $('#bloquea').css("display",'none');
                                                                                                        if(json =='error1'){
                                                                                                            bootbox.alert("El usuario ya existe") 
                                                                                                            }else{
                                                                                                                location.reload();
                                                                                                                //update progress bar
                                                                                                                /*$('#progress_text').html('40% Complete');
                                                                                                                $('#progress').css('width','160px');

                                                                                                                //slide steps
                                                                                                                $('#second_step').slideUp();
                                                                                                                $('#third_step').slideDown();*/

                                                                                                                }


                                                                                                            }
                                                                                                })
                                                                                    }else{
                                                                                            bootbox.alert("Es obligatorio indicar el nombre de un usuario que sea responsable del local");
                                                                                    }
                                                                                }
                                                                }
                                                        }
                                                    });
                                    }else{
                                        /* no se ha pedido un nuevo adm para el local*/
                                        
                                            form=document.getElementById('form-locales')
                                            $.ajax({
                                                        type: 'post',
                                                        url: $(form).attr('action'),
                                            data: {
                                                'datos':$(form).serialize(),      
                                                'alcance':arrAlcance,
                                                'tip':$("#h_local").val(),
                                                'editable':editable,
                                                'usu_email':$("#usu_email").val(),
                                                'usu_pass': $("#usu_pass").val()
                                                        },
                                            beforeSend:function(objeto){
                                                                            $('#bloquea').css("display",'block');
                                                                        },
                                                success: function(json) {
                                                        $('#bloquea').css("display",'none');
                                                        if(json =='error1'){
                                                            bootbox.alert("El usuario ya existe") 
                                                            }else{
                                                                location.reload();
                                                                //update progress bar
                                                                /*$('#progress_text').html('40% Complete');
                                                                $('#progress').css('width','160px');

                                                                //slide steps
                                                                $('#second_step').slideUp();
                                                                $('#third_step').slideDown();*/

                                                                }


                                                            }
                                                })
                                    }
                                    
                                    
                                    
                                /*}else{
                                    bootbox.alert("Seleccione los alcances de su delivery")
                                }*/
                                return false;
                        }
            })
            /**********************/
                     
        } else return false;

    });


    /*$('#submit_third').click(function(){
        //update progress bar
        $('#progress_text').html('60% Complete');
        $('#progress').css('width','240px');

        //prepare the fourth step
        var fields = new Array(
            $('#username').val(),
            $('#password').val(),
            $('#email').val(),
            $('#firstname').val() + ' ' + $('#lastname').val(),
            $('#age').val(),
            $('#gender').val(),
            $('#country').val()                       
        );
        var tr = $('#fourth_step tr');
        tr.each(function(){
            //alert( fields[$(this).index()] )
            $(this).children('td:nth-child(2)').html(fields[$(this).index()]);
        });
                
        //slide steps
        $('#third_step').slideUp();
        $('#fourth_step').slideDown();            
    });*/


    //$('#submit_fourth').click(function(){
        //send information to server
        //alert('Data sent');
    //});

});