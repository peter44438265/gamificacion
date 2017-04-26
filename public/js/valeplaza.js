	var url="https://www.valeplaza.com/cpn/api/filtro-tienda"; 
        $.ajax(url,{
            type: 'GET',
            async:false,
            contentType:"application/json",
            data:{
		'categoria':0,
		'like':$("#txt_nomtie").val(),
		'pais':1,
		'ciudad':1,
		'zona':0	
	    },
            dataType: 'jsonp',
            jsonp:'callback',
            success: function(data) {
               descuento_porcentage=0;
	       titulo="";
		 cad="";
                $(data.data).each(function(k, v){
			$(v.ofertas).each(function(a,b){
				descuento_porcentage=b.oferta_descuento	
				titulo=b.oferta_titulo
				if(b.pim_url ==null || b.pim_url=="null" || b.pim_url ==""){
	of_url="https://www.valeplaza.com/img/tag.png";
  }else{ 
	of_url="https://s3.amazonaws.com/pre.valeplaza.com/imagenes/producto/"+b.producto_id+"/"+b.pim_url;
  }
				});
                      	   if(v.tienda_logo == null || v.tienda_logo == 'null' || v.tienda_logo == '' ){
                       	      url="https://www.valeplaza.com/img/avatar-50x50.png";
                       	    }else{
                       	      url="https://s3.amazonaws.com/pre.valeplaza.com/imagenes/empresas/"+v.tienda_id+"/logo/"+v.tienda_logo;
                            }
if(v.tienda_fb_url == null || v.tienda_fb_url == 'null' || v.tienda_fb_url == '' ){
                        fb="";
                    }else{
                        fb=v.tienda_fb_url;
                    }
if(v.tienda_direccion == null || v.tienda_direccion == 'null' || v.tienda_direccion == '' ){
                        if(v.direccion == null || v.direccion == 'null' || v.direccion == '' ){
                            dire="";
                        }else{
                            dire=v.direccion;
                        }
                    }else{
                        dire=v.tienda_direccion;
                    }

			    cad+="<div class='informa'>";
                            cad+="<div class='logo'><a href='https://www.valeplaza.com/perfil/"+v.tienda_nick+"' target='_blank'><img src='"+url+"' width='100' height='100'  title='source: imgur.com' /></a></div>"; 
                            cad+="<div class='content-info'>";
                            cad+="<div class='titulo-tienda'>";
                            cad+="<a href='https://www.valeplaza.com/perfil/"+v.tienda_nick+"' target='_blank'><u>"+v.tienda_nombre+"</u></a>";  
                            cad+="</div>";   
                            cad+="<div class='descrip'>";
                            cad+="<p>"+v.tienda_descripcion_breve+"</p>";
                            cad+="</div>";  
                            cad+="<div class='facetienda'>";
                            cad+="<img  class='left' src='https://i.imgur.com/Q7M8wqq.png' title='source: imgur.com' /> <div class='url'><a href='"+fb+"'>"+fb+"</a> </div>";     
                            cad+="</div>";
                            cad+="</div>";

                            cad+="<div class='info-extra'>";

                            cad+="<div class='left'><img src='https://i.imgur.com/pERPynp.png' title='source: imgur.com' /></div>";
                            cad+="<div class='direccion'>"+dire+"</div>";  
                            cad+="<div class='left mascinco'><img src='https://i.imgur.com/h6PUUHs.png' title='source: imgur.com' /></div>";
                            cad+="<div class='list-tel'>";
                            cad+="<div class='telefono'><span>"+v.tienda_telefono+"</span></div>"; 
                            cad+="</div>";
                            cad+="</div>";

                            cad+="<div class='info-extra cienc'>";
                           if(v.ofertas !=""){
			    cad+="<div class='imgvale'>";
                            cad+="<div class='bonos' style='position:absolute'><span>-"+descuento_porcentage+"%</span></div>";

                            cad+="<img   width='165px' height='100' src='"+of_url+"' title='source: valeplaza' />";
                            cad+="<div class='buscarx'><input type='button' class='buscar' onclick=\"ver('"+v.tienda_id+"','"+v.tienda_nick+"')  \" value='Quiero Ofertas'></div>";

                            cad+="</div>";
                            cad+="<div class='text-vale'>";


                            cad+="<img  class='img_tra' src='https://i.imgur.com/FKdhIsa.png'>";
                            cad+="<p class='cont-p'>"+titulo+"</p>";
                            cad+="</div>";
			    }else{
			    cad+="<div class='imgvale'><div class='buscarx'><input type='button' class='buscar' onclick=\"ver('"+v.tienda_id+"','"+v.tienda_nick+"') \" value='Quiero Ofertas'></div></div>";
				}
                            cad+="</div>";

                            cad+="</div>";
                });
                $("#content-filtro").html(cad);
            },
            error: function(e){
                console.log(e.message)
            }
        });
        url_pais='https://www.valeplaza.com/cpn/api/listar-paises'
	$.ajax(url_pais,{
		type:'POST',
		async:'false',
		contentType:'application/json',
		dataType:'jsonp',
		jsonp:'callback',
		success: function(result){
			cad="<option value=''>Todos</option>"
                        $(result.data).each(function(k, v){

                            cad+="<option value='"+v.pais_id+"'";
                            if(v.pais_id == 1){
                                cad+="selected='selected'";
                            }
                            cad+=">"+v.pais_nombre+"</option>";
                        })
                        $("#flt_pais").append(cad);
		 },
		error: function(e){ 
			console.log(e.message);
		 }		

		});

	url_ciudad='https://www.valeplaza.com/cpn/api/listar-ciudades'
        $.ajax(url_ciudad,{
		type:'POST',
		async:'false',
		contentType:'application/json',
		dataType:'jsonp',
		jsonp:'callback',
                data: {
                    'pais': 1
                },
		success: function(result){
			cad="<option value=''>Todos</option>"
                        $(result.data).each(function(k, v){

                            cad+="<option value='"+v.prov_id+"'"
                            if(v.prov_id==1){
                                cad+="selected='selected'";
                            }
                            cad+=">"+v.prov_nombre+"</option>";
                        })
                        $("#flt_ciudad").empty();
                        $("#flt_ciudad").append(cad);
		 },
		error: function(e){ 
			console.log(e.message);
		 }		

		});
	$("#flt_pais").change(function(){
        $.ajax(url_ciudad,{
		type:'POST',
		async:'false',
		contentType:'application/json',
		dataType:'jsonp',
		jsonp:'callback',
                data: {
                    'pais': $("#flt_pais").val()
                },
		success: function(result){
			cad="<option value=''>Todos</option>"
                        $(result.data).each(function(k, v){

                            cad+="<option value='"+v.prov_id+"'>"+v.prov_nombre+"</option>";
                        })
                        $("#flt_ciudad").empty();
                        $("#flt_ciudad").append(cad);
		 },
		error: function(e){ 
			console.log(e.message);
		 }		

		});
        })
        
         url_zona='https://www.valeplaza.com/cpn/api/listar-zonas'
	$("#flt_ciudad").change(function(){
        $.ajax(url_zona,{
		type:'POST',
		async:'false',
		contentType:'application/json',
		dataType:'jsonp',
		jsonp:'callback',
                data: {
                    'ciudad': $("#flt_ciudad").val()
                },
		success: function(result){
			cad="<option value=''>Todos</option>"
                        $(result.data).each(function(k, v){

                            cad+="<option value='"+v.mall_id+"'>"+v.mall_nombre+"</option>";
                        })
                        $("#flt_zona").append(cad);
		 },
		error: function(e){ 
			console.log(e.message);
		 }		

		});
        })
	url_ver='https://www.valeplaza.com/cpn/api/sumar-visita'
   function ver(id,nick){
	//window.parent.location.href="https://www.valeplaza.com/perfil/"+nick;
	//window.open("https://www.valeplaza.com/perfil/"+nick,'_blank');
	$.ajax(url_ver,{
		type:'POST',
		async:'false',
		contentType:'application/json',
		dataType:'jsonp',
		jsonp:'callback',
		data:{
			id:id
		 },
		success: function(result){
			window.open("https://www.valeplaza.com/perfil/"+nick,'_blank');
		 },
		error: function(e){ 
			console.log(e.message);
		 }		

		});

	}
   var url='https://www.valeplaza.com/cpn/api/listar-categoria'; 
   $.ajax(url,{
	type:'GET',
        async: false,
        contentType: "application/json",	
        dataType: 'jsonp',
	jsonp:'callback',
	success: function(result) {
         cad=""
         $(result.data).each(function(k, v){
            
            cad+="<option value='"+v.rubro_id+"'>"+v.rubro_nombre+"</option>";
         })
         $("#slcategoriavp").append(cad);
        },
        error: function(e) {
           console.log(e.message);
        }
 });
$("#btn-buscar").click(function(){
        var url="https://www.valeplaza.com/cpn/api/filtro-tienda"; 
        $.ajax(url,{
            type: 'GET',
            async:false,
            contentType:"application/json",
            data:{
		'categoria':$("#slcategoriavp").val(),
		'like':$("#txt_nomtie").val(),
		'pais':$("#flt_pais").val(),
		'ciudad':$("#flt_ciudad").val(),
		'zona':$("#flt_zona").val()	
	    },
            dataType: 'jsonp',
            jsonp:'callback',
            success: function(data) {
               descuento_porcentage=0;
	       titulo="";
		 cad="";
                $(data.data).each(function(k, v){
			$(v.ofertas).each(function(a,b){
				descuento_porcentage=b.oferta_descuento	
				titulo=b.oferta_titulo
				if(b.pim_url ==null || b.pim_url=="null" || b.pim_url ==""){
	of_url="https://www.valeplaza.com/img/tag.png";
  }else{ 
	of_url="https://s3.amazonaws.com/pre.valeplaza.com/imagenes/producto/"+b.producto_id+"/"+b.pim_url;
  }
				});
                      	   if(v.tienda_logo == null || v.tienda_logo == 'null' || v.tienda_logo == '' ){
                       	      url="https://www.valeplaza.com/img/avatar-50x50.png";
                       	    }else{
                       	      url="https://s3.amazonaws.com/pre.valeplaza.com/imagenes/empresas/"+v.tienda_id+"/logo/"+v.tienda_logo;
                            }
if(v.tienda_fb_url == null || v.tienda_fb_url == 'null' || v.tienda_fb_url == '' ){
                        fb="";
                    }else{
                        fb=v.tienda_fb_url;
                    }
if(v.tienda_direccion == null || v.tienda_direccion == 'null' || v.tienda_direccion == '' ){
                        if(v.direccion == null || v.direccion == 'null' || v.direccion == '' ){
                            dire="";
                        }else{
                            dire=v.direccion;
                        }
                    }else{
                        dire=v.tienda_direccion;
                    }

			    cad+="<div class='informa'>";
                            cad+="<div class='logo'><a href='https://www.valeplaza.com/perfil/"+v.tienda_nick+"' target='_blank'><img src='"+url+"' width='100' height='100'  title='source: imgur.com' /></a></div>"; 
                            cad+="<div class='content-info'>";
                            cad+="<div class='titulo-tienda'>";
                            cad+="<a href='https://www.valeplaza.com/perfil/"+v.tienda_nick+"' target='_blank'><u>"+v.tienda_nombre+"</u></a>";  
                            cad+="</div>";   
                            cad+="<div class='descrip'>";
                            cad+="<p>"+v.tienda_descripcion_breve+"</p>";
                            cad+="</div>";  
                            cad+="<div class='facetienda'>";
                            cad+="<img  class='left' src='https://i.imgur.com/Q7M8wqq.png' title='source: imgur.com' /> <div class='url'><a href='"+fb+"'>"+fb+"</a> </div>";     
                            cad+="</div>";
                            cad+="</div>";

                            cad+="<div class='info-extra'>";

                            cad+="<div class='left'><img src='https://i.imgur.com/pERPynp.png' title='source: imgur.com' /></div>";
                            cad+="<div class='direccion'>"+dire+"</div>";  
                            cad+="<div class='left mascinco'><img src='https://i.imgur.com/h6PUUHs.png' title='source: imgur.com' /></div>";
                            cad+="<div class='list-tel'>";
                            cad+="<div class='telefono'><span>"+v.tienda_telefono+"</span></div>"; 
                            cad+="</div>";
                            cad+="</div>";

                            cad+="<div class='info-extra cienc'>";
                           if(v.ofertas !=""){
			    cad+="<div class='imgvale'>";
                            cad+="<div class='bonos' style='position:absolute'><span>-"+descuento_porcentage+"%</span></div>";

                            cad+="<img   width='165px' height='100' src='"+of_url+"' title='source: valeplaza' />";
                            cad+="<div class='buscarx'><input type='button' class='buscar' onclick=\"ver('"+v.tienda_id+"','"+v.tienda_nick+"')  \" value='Quiero Ofertas'></div>";

                            cad+="</div>";
                            cad+="<div class='text-vale'>";


                            cad+="<img  class='img_tra' src='https://i.imgur.com/FKdhIsa.png'>";
                            cad+="<p class='cont-p'>"+titulo+"</p>";
                            cad+="</div>";
			    }else{
			    cad+="<div class='imgvale'><div class='buscarx'><input type='button' class='buscar' onclick=\"ver('"+v.tienda_id+"','"+v.tienda_nick+"') \" value='Quiero Ofertas'></div></div>";
				}
                            cad+="</div>";

                            cad+="</div>";
                });
                $("#content-filtro").html(cad);
            },
            error: function(e){
                console.log(e.message)
            }
        });
   });
