$(document).ready(function(){
    
	var displayProduct = 5;
	$('#results').html(createSkeleton(displayProduct));
	
    setTimeout(function(){
      loadProducts(displayProduct);
    }, 5000);

    function createSkeleton(limit){
      var skeletonHTML = '';
      for(var i = 0; i < limit; i++){
        skeletonHTML += '<div class="ph-item">';
        skeletonHTML += '<div class="ph-col-4">';
        skeletonHTML += '<div class="ph-picture"></div>';
        skeletonHTML += '</div>';
        skeletonHTML += '<div>';
        skeletonHTML += '<div class="ph-row">';
        skeletonHTML += '<div class="ph-col-12 big"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '</div>';
        skeletonHTML += '</div>';
        skeletonHTML += '</div>';
      }
      return skeletonHTML;
    }
	
    function loadProducts(limit){
      $.ajax({
     url:"http://sellercenter.twinzahra.com/api/orders.php?request=get_rts",
        method:"POST",
        data:{action: 'load_products', limit:limit},
        success:function(data) {
          $('#results').html(data);
        }
      });
    }

  });