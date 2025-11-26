<style>
/* Fixed sidenav, full height */
.sidenav {
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  overflow-x: hidden;
}

/* Style the sidenav links and the dropdown button */
.sidenav a, .dropdown-btn {
  text-decoration: none;
  display: block;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  outline: none;
  padding: .5rem!important;
}
/* On mouse-over */


.button {
    border: none;
}

.space{
  padding-bottom: 8px;
}

/* Dropdown container (hidden by default). Optional: add a lighter background color and some left padding to change the design of the dropdown content */
.dropdown-container {
  display: none;
  padding-left:10px;
}

/* Optional: Style the caret down icon */
.fa-caret-down {
  float: right;
  padding-right: 8px;
}

/* Some media queries for responsiveness */
@media screen and (max-height: 450px) {
  .sidenav {
    padding-top: 15px;
    margin-left: -220px;
    }
  .sidenav a {font-size: 18px;}
  .main_conten{
    margin-left: 0px;
   
  }
  .footer_main_content{
    margin-left: 0px; 
  }
}
.offerCount{
  padding: 0px 15px;
    border: 2px solid orangered;
    margin-left: 5px;
    border-radius: 4px;
    color: orangered;
}
.cat-name{
font-size:12px;
}
</style>
<div class="aiz-category-menu bg-white rounded <?php if(Route::currentRouteName() == 'home'): ?> shadow-sm" <?php else: ?> shadow-lg" id="category-sidebar" <?php endif; ?>>
  <?php
      use Carbon\Carbon;

      $currentDate = Carbon::now();

      $offers = \App\Models\Offer::where('status', 1)
          ->where('start_date', '<=', strtotime($currentDate))
          ->where('end_date', '>=', strtotime($currentDate))
          ->get();

      $conditions = ['published' => 1];
      $products = \App\Models\Product::where($conditions)
          ->where('discount', '>', 0);

      $nonPaginateProducts = filter_products($products)->get();

      $offerCount = $nonPaginateProducts->count();
  ?>

  <ul class="list-unstyled categories no-scrollbar py-2 mb-0 text-left" >
  <a href="<?php echo e(route('offers')); ?>" style="font-size: 15px; color: #AE3C86!important; font-weight: bold; margin-top: 60px;">
      <span class="cat-name">Offers</span>
      <span class="offerCount"><?php echo e($offerCount); ?></span>
  </a>
</ul>


  <?php
    echo file_get_contents(base_path('category_menu_static.php'), true);
  ?>
</div>







<script>

function getSubCategory(cat_id){

  var str = '<div style="min-height:600px;"><div class="row"><div class="col-md-4"><hr style="background:grey;height:2px;"></div><div class="col-md-4" style="font-size:24px;font-weight:bold;">'+$('#cat_name_'+cat_id).html()+'</div><div class="col-md-4"><hr style="background:grey;height:2px;"></div><div class="clearfix"></div></div><div class="row">';
  //var str = '<div style="min-height:600px;"><div class="row">'+$('.maincategory_'+cat_id).html()+'</div><div class="row"><div class="col-md-4"><hr style="background:grey;height:2px;"></div><div class="col-md-4" style="font-size:24px;font-weight:bold;">'+$('#cat_name_'+cat_id).html()+'</div><div class="col-md-4"><hr style="background:grey;height:2px;"></div><div class="clearfix"></div></div><div class="row">';
  $('.subcategory_'+cat_id).each(function(i,v){
    str+='<div class="col-md-3 space"  style="text-align:center;">'+$(this).html()+'</div>';
  });
  str+='<div class="clearfix"></div></div></div>';
  $('.main_content').html(str);

	if($('#cat_name_'+cat_id).next('i').hasClass('la-chevron-down')==true){
		$('#cat_name_'+cat_id).next('i').removeClass('la-chevron-down');
    	$('#cat_name_'+cat_id).next('i').addClass('la-chevron-right');
	}else{
    	$('#cat_name_'+cat_id).next('i').removeClass('la-chevron-right');
    	$('#cat_name_'+cat_id).next('i').addClass('la-chevron-down');
    }
}

/* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
var dropdown = document.getElementsByClassName("dropdown-btn");
var i;

for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
  this.classList.toggle("active");
  var dropdownContent = this.nextElementSibling;
  if (dropdownContent.style.display === "block") {
  dropdownContent.style.display = "none";
  $(this).find('.la-chevron-right').removeClass('la-chevron-down').addClass('la-chevron-right');
  } else {
  dropdownContent.style.display = "block";
  $(this).find('.la-chevron-right').removeClass('la-chevron-right').addClass('la-chevron-down');
  }
  });
}
</script><?php /**PATH D:\xampp\htdocs\bazarnao\resources\views/frontend/partials/category_menu.blade.php ENDPATH**/ ?>