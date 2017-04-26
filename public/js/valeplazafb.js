$(document).ready(function() {
   
   $.ajax({
      type: 'GET',
      url: 'http://pre.valeplaza.com/cpn/api/listar-categoria',
      beforeSend : function(){  
      },
      onComplete: function(json) {
         alert("entro")
         
      }
   })
})