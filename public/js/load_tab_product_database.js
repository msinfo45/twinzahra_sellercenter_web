
$(document).on("click", "#tab-database", function () {
  
  loadClassProductDatabase();
});

loadClassProductDatabase();

 function loadClassProductDatabase(SearchProductDatabase){
    
	var displayProduct = 5;
    var Page = 1;
	$('#ResultProductDatabase').html(createSkeleton(displayProduct));
	
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
         url:"http://localhost/twinzahra/Tab_database_product",
       data:{action: 'load_products', limit:limit , "Search":SearchProductDatabase,"Page": Page},
        success:function(data) {
           $('#ResultProductDatabase').html(data); 
        }
      });
    }

}