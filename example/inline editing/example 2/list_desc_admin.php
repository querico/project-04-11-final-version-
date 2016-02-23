<?php
    

  if (! (isset($_GET['gunlist']) )) { header('Location: index_admin.php'); exit; }
  $gunlistID = $_GET['gunlist'];

  $_SESSION["token"] = "123abc";
  $token = password_hash("123abc",PASSWORD_DEFAULT);

  define('NAV', 'armsbook');
  include($_SERVER['DOCUMENT_ROOT']."/lib/template/header.php"); 
  include_once($_SERVER['DOCUMENT_ROOT']."/lib/scripts/config.php");
$_SESSION["token"] = "123abc";
  $token = password_hash("123abc",PASSWORD_DEFAULT);


  $gunlistObject = new GunlistModel($mysqli);

  $gunlistName = "unknown"; //TODO

$gunlistInfo = $gunlistObject->getGunlist($gunlistID);
if (isset($gunlistInfo["gunlist_name"])){
  $gunlistName = $gunlistInfo["gunlist_name"];
}

$armsObj = new armsbookModel($mysqli);


  $dateNow = new DateTime();

function formatPercent($value, $decimalPlaces=2){
  if ($value = 0) return "";
  
  return number_format( (100 * $value) , $decimalPlaces ) . "%";
}

$result = $armsObj->getCategories();
                    $categories = array();
                    $count = 0;
                    foreach($result as $category) {
                        array_push($categories, array('value' => $category["id"], 'text' => $category["category"]));
                    }
                    
$calibers = $armsObj->getCalibers();
$cartridges = $armsObj->getCartridges();
$result = $armsObj->getLicences();
$licences = array();
                    $count = 0;
                    foreach($result as $licence) {
                        array_push($licences, array('value' => $licence["id"], 'text' => $licence["grade"]));
                    }
$result = $armsObj->getFinishes();
$finishes = array();
                    $count = 0;
                    foreach($result as $finish) {
                        array_push($finishes, array('value' => $finish["id"], 'text' => $finish["finish"]));
                    }
$result = $armsObj->getActions();
$actions = array();
$count = 0;
                    foreach($result as $action) {
                        array_push($actions, array('value' => $action["id"], 'text' => $action["action"]));
                    }
                    
$result = $armsObj->getChokes();
$chokes = array();
$count = 0;
                    foreach($result as $choke) {
                        array_push($chokes, array('value' => $choke["id"], 'text' => $choke["choke"]));
                    }

?>

<!--<pre><?php print_r($calibers);?></pre>-->
	<!--X-editable [ OPTIONAL ]-->
	<link href="/lib/template/plugins/x-editable/css/bootstrap-editable.css" rel="stylesheet">

<style>

.actionNeeded{
    background-color: #FF8B8B;
}

.text-right{
  padding-right:15px !important;  
}

.edit-area, .move-down, .move-up{
  padding:0px;
  font-size:1.5em;
}

tr.category-header td{
  font-weight: bold;
  background-color: #5bc0de;
  font-size:2em;
}

.accessories{
  font-size : 10px;
}

.area-header > th{
  background-color: #555;
  height : 40px !important;
  color : #fff;
  font-size : 1.2em !important;
}

.new-description{
  background-color: #D3E5FF;
  width:230px;
}

.new-description > input{
  width:220px;
}

.show_special{
  width:220px;
}

.edit_special{
  display: none;
}

.price-change-up{
  background-color: #FCFF81;
}

.price-change-down{
  
}

.area_title{
  font-size:1.2em;
  font-weight:bold;
}

.tiny{
  font-size:10px;
}


</style>




<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-mint">
      <div class="panel-heading">
        <h2 class="panel-title">GunList : : <?php echo $gunlistName; ?></h2>
      </div>
      <div class="panel-body" >

        <table class="table table-striped table-condensed">
          <tbody>
<?php

            
            foreach ($gunlistObject->getAllItems($gunlistID) as $category) {
              echo "<tr class='category-header'><td colspan='19'>".$category['category_name']."</td></tr>";
              foreach ($category['areas'] as $area) {
                $actionRequired = false;
                if (strpos($area["name"],'[ACTION]') !== false) $actionRequired = true;
                
?>
                <tr class='area-header' >
                    <th>Edit<br><input type="checkbox" class="checkAll" x-list-id="<?php echo $area['id']; ?>"></th>
                  <th colspan=4><span class='area_title'><?php echo $area["name"]; ?></span><br><span class='tiny'>(id:<?php echo $area['id']; ?>)</span></th>
                  <th>Category</th>
                  <th>Caliber</th>
                  <th>Cartridge</th>
                  <th>Licence</th>
                  <th>Finish</th>
                  <th>Action</th>
                  <th>Choke</th>
                  <th>Attributes</th>
                  <th class='text-center'></th>
                  <th class='text-right'>Current<br/>Special</th>
                  <th>Last Sale Desc</th>
                  <th>Gunlist Desc</th>
                  <th class='text-center'>Char<br/>Count</th>
                  <th></th>
                </tr>


<?php
                  if (isset($area["items"])){
                  foreach ($area["items"] as $item) {
                      
                      /*
                      echo "<pre>";
                      print_r($item);
                      echo "</pre>";
                      */
                      
                    $prefix = $item["prefix"];
                    $armsbook_id = $item["armsbook_id"];
                    $book_id = $item["book_id"];
                    $cat_id = $item["ab_category_id"];
                    $cal_id = $item["ab_caliber_id"];
                    $rrp = $item["retail_inc"];
                    $current_special = $item["current_special_inc"];
                    $new_special = $item["new_special_inc"];
                    $last_sale_new = $item["last_new_special"];
                    $last_sale_current = $item["last_current_special"];
                    $last_description = $item["last_description"];

                    $attributes = "";
                    if ( $item['barrel_length'] != null && $barrel_length > 0 ) $attributes .= " Barrel-Length:" . $barrel_length;
                    
                    if (isset($item['capacity'])){
                      if ( $item['capacity'] != null ) $attributes .= " Capacity:" . $item['capacity'];
                    }

                    if ($item['attr_fbs']) $attributes .= " FBS";
                    if ($item['attr_hb']) $attributes .= " HB";
                    if ($item['attr_tfs']) $attributes .= " TFS";
                    if ($item['attr_thole']) $attributes .= " T-Hole";
                    if ($item['attr_comb']) $attributes .= " adj-comb";
                    if ($item['attr_lh']) $attributes .= " LH";

                    $charCount = strlen($item["package_description"]);
                    if ($charCount > 30) $charCountLabel = "label-danger";
                    else if ($charCount > 25) $charCountLabel = "label-warning";
                    else $charCountLabel = "label-primary";

                    if ($current_special==0 || $new_special==0) $discount = 0;
                    else {
                       $discount = 100 * ($current_special-$new_special) / $current_special;
                    }

?>
                <tr x-armsbook_id="<?=$armsbook_id; ?>" x-book_id="<?=$book_id; ?>" x-list-id="<?php echo $area['id']; ?>" id='row_<?php echo $item["id"]; ?>' data-id='<?php echo $item["id"]; ?>' class="gunlist_item">
                    <td id="check"><input type="checkbox" class="checkEdit" x-list-id="<?php echo $area['id']; ?>"></td>
                  <td id="id-col">
                    <strong><a id="book-id" target='_blank' href='/armsbook/record_edit.php?book=<?php echo $item['book_id']; ?>&record=<?php echo $item['armsbook_id']; ?>'><?php echo $prefix.$armsbook_id; ?></a></strong><br>(<?php echo $item["id"]; ?>)

                  </td>

                   <td><button class='edit-area btn' data-id="<?php echo $item["id"]; ?>" data-armsbookno="<?php echo $prefix.$armsbook_id; ?>" id='area_<?php echo $item["id"]; ?>'><i class="fa fa-pencil-square-o"></i></button></td>

                  <td width='300px'>
<?php 
                    foreach ($item['accessories'] as $accessory) {
                      echo "<div class='pull-left'>".$accessory["description"]."</div>";
                    }
?>
                  </td>



<td><?php echo $item['comment']; ?></td>
<td id="category" class="category" x-id="<?php echo $cat_id; ?>"><?php echo $item['category']; ?></td>
<td class="caliber" x-id="<?php echo $cal_id; ?>"><?php echo $item['caliber'];?></td>
<td class="cartridge"><?php echo $item['cartridge']; ?></td>
<td class="licence"><?php echo $item['licence']; ?></td>
<td class="finish"><?php echo $item['finish']; ?></td>


<td class='action <?php echo ($actionRequired && $item['action']=="" ? "actionNeeded" : "") ?>'><?php echo $item['action']; ?></td>


<td class="choke"><?php echo $item['choke']; ?></td>


<td><?php echo $attributes; ?></td>




                  

                  
                  <td class='pull-center text-center'><span class='label <?php echo ( $item["new"] ? "label-primary" : "label-warning" ); ?>'><?php echo ( $item["new"] ? "NEW" : "S/H" ); ?></span></td>

                  
                  <td class='text-right'><?php echo number_format($current_special); ?></td>

                  <td><?php echo $last_description; ?></td>
                  
                  <td class='new-description'>
                    <input data-id="<?php echo $item["id"]; ?>" class='edit_special' type='text' id='edit_<?php echo $item["id"]; ?>' value='<?php echo $item["package_description"]; ?>' >
                    <div class='show_special' id='<?php echo $item["id"]; ?>'><?php echo $item["package_description"]; ?></div>
                  </td>

                  <td class='text-center'>
                    
                    <span id='count_<?php echo $item["id"]; ?>' class='label <?php echo $charCountLabel; ?>'><?php echo $charCount; ?></span>
                  </td>

                  <td><button class='move-up btn' data-id="<?php echo $item["id"]; ?>" id='up_<?php echo $item["id"]; ?>'><i class="fa fa-arrow-circle-up"></i></button>
                  <button class='move-down btn' data-id="<?php echo $item["id"]; ?>" id='down_<?php echo $item["id"]; ?>'><i class="fa fa-arrow-circle-down"></i></i></button></td>

                </tr>

<?php
                  }
                  }
                }
              }
                
?>


              </tbody>
            </table>
          
        </div>
      </div>
    </div>
  </div>

  <!--Default Bootstrap Modal  -  NEW  -->
  <!--===================================================-->
  <div class="modal" id="modal_area" role="dialog" tabindex="-1" aria-labelledby="modal_area" aria-hidden="true">
    <div class="modal-dialog modal-md" >
      <div class="modal-content">

        <!--Modal header-->
        <div class="modal-header" style="padding-top:4px; padding-bottom:4px">
          <button data-dismiss="modal" class="close" type="button">
          <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="modal_area-title">Select Area</h4>
        </div>

        <!--Modal body-->
        <div class="modal-body" style="padding-top:5px">



          <h3 id='mondal_area-bookNumber'>Book Number</h3>
          <div id='modal_area-description'>Firearm Package</div>

          <lable for='area'>Area</label>
          <select name='area' id='modal_area-area'>
            <?php


            foreach ($gunlistObject->getAreas($gunlistID) as $category) {
              
              echo "<option disabled>──────────</option>";
              echo "<option disabled>".$category['category_name']."</option>";
              foreach ($category['areas'] as $gunlistArea) {
                echo "<option value='".$gunlistArea['id']."''>".$gunlistArea['name']."</option>";
              }
            }

            ?>
          </select>
        </div>

        <!--Modal footer-->
        <div class="modal-footer">
          <button class="btn btn-info" type="submit" id="modal_area-submit">Submit</button>
        </div>


      </div>
    </div>
  </div>
  <!--===================================================-->
  <!--End Default Bootstrap Modal-->
    
	<!--X-editable [ OPTIONAL ]-->
        <script src="/lib/template/js/bootstrap.js"></script>
	<script src="/lib/template/plugins/x-editable/js/bootstrap-editable.js"></script>
        
<script src="/lib/template/js/jquery.number.min.js" type="text/javascript"></script>
<script>
    $.fn.scrollTo = function( target, options, callback ){
  if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
  var settings = $.extend({
    scrollTarget  : target,
    offsetTop     : 50,
    duration      : 500,
    easing        : 'swing'
  }, options);
  return this.each(function(){
    var scrollPane = $(this);
    var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
    var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
    scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
      if (typeof callback == 'function') { callback.call(this); }
    });
  });
}
    
    var calibers = $.parseJSON('<?php echo json_encode($calibers) ?>');
    var cartridges = $.parseJSON('<?php echo json_encode($cartridges) ?>');
    var licences = $.parseJSON('<?php echo json_encode($licences) ?>');
    var categories = $.parseJSON('<?php echo json_encode($categories) ?>');
    var finishes = $.parseJSON('<?php echo json_encode($finishes) ?>');
    var actions = $.parseJSON('<?php echo json_encode($actions) ?>');
    var chokes = $.parseJSON('<?php echo json_encode($chokes) ?>');

$(document).ready(function () {
    
    $(".checkAll").change(function () {
        var list = $(this).attr("x-list-id");
        if(this.checked) {
            $("input.checkEdit[x-list-id="+ list +"]").prop("checked", true);
        } else {
            $("input.checkEdit[x-list-id="+ list +"]").prop("checked", false);
        }
        
    });
    
    $("input.checkEdit").change(function () {
        console.log(this + "Has been checked");
        var list = $(this).attr("x-list-id");
        var checks = $("input.checkEdit[x-list-id="+ list +"]");
        var isChecked = false;
        $.each(checks, function (i, check) {
           if(check.checked) {
               isChecked = true;
               return;
           } 
        });
        if(isChecked) {
            $("input.checkAll[x-list-id="+ list +"]").prop("checked", true);
        } else {
            $("input.checkAll[x-list-id="+ list +"]").prop("checked", false);
        }
        
    });
    
    $(".editableform").submit(function (){
        
        $(this).closest('tr').scrollTo();
    });

    
    
    function getArrayBySelector(array, selector) {
        var vtArray = [];
        
        array.forEach(function (item) {
            var output = selector(item);
            if(output != null) {
                vtArray.push(selector(item));
            }
        });
        if(vtArray.length < 1) {vtArray = null;}
        return vtArray;
    }
    
//    //do this as decorator thingy
//    function getIdFromWhere(array, text, textField, idField) {
//        
//        if(text != "Empty") {
//            var arr = $.grep(array, function(e){return e[textField] === text; });
//            return arr[0][idField];
//        } else {
//            
//            return;
//        }
//        
//    }

    function updateChecked(reason, elem, selector) {
        if(reason == 'save') {
            var list = $(elem).parent().attr('x-list-id');
            if($(elem)
                .siblings('td#check')
                .children('input.checkEdit')
                .prop('checked')) {
                    var checked = $("tr[x-list-id="+ list +"] > td#check > input.checkEdit:checked")
                        .parent()
                        .siblings(selector)
                        .not(elem);
                        console.log(checked);
                        $.each(checked, function (i, item) {
                            $(item).trigger("mousedown");
                            $(item).editable('setValue', $(elem).attr("set-value"));
                            $(item).editable('submit');
                        });
//                    checked.trigger("mousedown");
//                    checked.editable('setValue', $(elem).attr("set-value"));
//                    checked.editable('submit');   
            }
        }
    }

    
    $.fn.editable.defaults.mode = 'inline';
    // X-EDITABLE USING FONT AWESOME ICONS
	// =================================================================
    $.fn.editableform.buttons =
        '<button type="submit" class="btn btn-primary editable-submit">'+
                '<i class="fa fa-fw fa-check"></i>'+
        '</button>'+
        '<button type="button" class="btn btn-default editable-cancel">'+
                '<i class="fa fa-fw fa-times"></i>'+
        '</button>';

    $('.category').editable({
		type: 'select',
		title: 'Select Category',
		pk: 0,
                source: JSON.stringify(categories),
                savenochange: true,
		params: function (params) {
			var data = {};
			data.armsbook_id = $(this).parent().attr("x-armsbook_id");
                        data.book_id = $(this).parent().attr("x-book_id");
			data.action = "update_category";
			data.value =  params.value;                    
                        $(this).attr("x-id", params.value );
			return data;
		},
                success: function (res, val) {
                             $(this).attr("set-value", val);          
                    $(this).siblings('.caliber').html("");
                    $(this).siblings('.caliber').editable('setValue',0);
                    $(this).siblings('.caliber').editable('submit');
                    
                    
    },     
		url: '/lib/scripts/api/armsbook.json.php'
	});
     
        $('.caliber').editable({
        type: 'select',
        title: 'Select Caliber',
        pk: 0,
        savenochange: true,
        sourceError: "Please Select a Category",
        params: function (params) {
                var data = {};
                data.armsbook_id = $(this).parent().attr("x-armsbook_id");
                data.book_id = $(this).parent().attr("x-book_id");
                data.action = "update_caliber";
                data.value =  params.value;
                $(this).attr("x-id", params.value );
                return data;
        },
        success: function (res, val) {
            $(this).attr("set-value", val);
            $(this).siblings('.cartridge').html("");
            $(this).siblings('.cartridge').editable('setValue',0);
            $(this).siblings('.cartridge').editable('submit');
    },
        url: '/lib/scripts/api/armsbook.json.php'
    });
    
    $('.cartridge').editable({
        type: 'select',
        title: 'Select Cartridge',
        pk: 0,
        savenochange: true,
        sourceError: "Please Select a Caliber",
        params: function (params) {
                var data = {};
                data.armsbook_id = $(this).parent().attr("x-armsbook_id");
                data.book_id = $(this).parent().attr("x-book_id");
                data.action = "update_cartridge";
                data.value =  params.value;
                return data;
        },
        url: '/lib/scripts/api/armsbook.json.php',
        success: function (res, val) {
            $(this).attr("set-value", val);
        }
    });
    
    $('.licence').editable({
        type: 'select',
        title: 'Select Licence',
        pk: 0,
        savenochange: true,
        source: JSON.stringify(licences),
        params: function (params) {
                var data = {};
                data.armsbook_id = $(this).parent().attr("x-armsbook_id");
                data.book_id = $(this).parent().attr("x-book_id");
                data.action = "update_licence";
                data.value =  params.value;
                return data;
        },
        url: '/lib/scripts/api/armsbook.json.php',
        success: function (res, val) {
            $(this).attr("set-value", val);
        }
    });
    
    $('.finish').editable({
        type: 'select',
        title: 'Select Finish',
        pk: 0,
        savenochange: true,
        source: JSON.stringify(finishes),
        params: function (params) {
                var data = {};
                data.armsbook_id = $(this).parent().attr("x-armsbook_id");
                data.book_id = $(this).parent().attr("x-book_id");
                data.action = "update_finish";

                data.value =  params.value;
                return data;
        },
        url: '/lib/scripts/api/armsbook.json.php',
        success: function (res, val) {
            $(this).attr("set-value", val);
        }
    });
    
    $('.action').editable({
        type: 'select',
        title: 'Select Action',
        pk: 0,
        savenochange: true,
        source: JSON.stringify(actions),
        params: function (params) {
                var data = {};
                data.armsbook_id = $(this).parent().attr("x-armsbook_id");
                data.book_id = $(this).parent().attr("x-book_id");
                data.action = "update_action";
                console.log(params.value);
                data.value =  params.value;
                return data;
        },
        url: '/lib/scripts/api/armsbook.json.php',
        success: function (res, val) {
            $(this).attr("set-value", val);
        }
    });
    
    $('.choke').editable({
        type: 'select',
        title: 'Select Choke',
        pk: 0,
        savenochange: true,
        source: JSON.stringify(chokes),
        params: function (params) {
                var data = {};
                data.armsbook_id = $(this).parent().attr("x-armsbook_id");
                data.book_id = $(this).parent().attr("x-book_id");
                data.action = "update_choke";
                data.value =  params.value;
                return data;
        },
        url: '/lib/scripts/api/armsbook.json.php',
        success: function (res, val) {
            $(this).attr("set-value", val);
        }
    });
    
    $('.caliber').mousedown(function () {
        var id = $(this).siblings(".category").attr('x-id');
        console.log(id);
        var cals = getArrayBySelector(calibers, function(cal) {
            if(cal['category_id'] == id) {

                return {value : cal['id'], text: cal['name']};
            }
        });
        $(this).editable('option', 'source', cals);
         
    });
    
    $('.cartridge').mousedown(function () {
        var id = $(this).siblings(".caliber").attr('x-id');
        var carts = getArrayBySelector(cartridges, function(cart) {
            if(cart['caliber_id'] == id) {

                return {value : cart['id'], text: cart['cartridge']};
            }
        });
        $(this).editable('option', 'source', carts);   
    });
    
    $('.category').on('hidden', function(e, reason) {
        updateChecked(reason, this, ".category");
    });
    
    $('.caliber').on('hidden', function(e, reason) {
        updateChecked(reason, this, ".caliber");
    });
    
    $('.cartridge').on('hidden', function(e, reason) {
        updateChecked(reason, this, ".cartridge");
    });
    
    $('.licence').on('hidden', function(e, reason) {
       updateChecked(reason, this, ".licence");
    });
    
    $('.finish').on('hidden', function(e, reason) {
        updateChecked(reason, this, ".finish");
    });
    
    $('.action').on('hidden', function(e, reason) {
        updateChecked(reason, this, ".action");
    });
    
    $('.choke').on('hidden', function(e, reason) {
        updateChecked(reason, this, ".choke");
    });
});      
    


$(".show_special").click(function(){
  $("#edit_"+this.id).show();
  $("#edit_"+this.id).select();
  $(this).hide();
});



$(".edit_special").keyup(function (e) {
      var id = $(this).data("id");
      var showSpan = $("#"+id);



      if ( e.keyCode == 13 ) {

        
      

        $(this).hide();
        showSpan.show();
        
        var nextInput = $(this).closest("tr").next('tr').find('.edit_special')
        nextInput.show();
        nextInput.select();
        nextInput.next('.show_special').hide();
        showSpan.text($(this).val());
        setDescription(id, $(this).val());

      } else if ( e.keyCode == 40 ) { //up

      

        $(this).hide();
        showSpan.show();
        
        var nextInput = $(this).closest("tr").next('tr').find('.edit_special')
        nextInput.show();
        nextInput.select();
        nextInput.next('.show_special').hide();        




      } else if ( e.keyCode == 38 ) { //up

      

        $(this).hide();
        showSpan.show();
        
        var nextInput = $(this).closest("tr").prev('tr').find('.edit_special')
        nextInput.show();
        nextInput.select();
        nextInput.next('.show_special').hide();

      } else if ( e.keyCode == 27 ) { //esc

        $(this).hide();
        showSpan.show();
      } else {

        $("#count_"+id).text( $(this).val().length );

      }



});


var token = '<?php echo $token; ?>';
function setDescription(gunlistItemID, newDescription){
  console.log("sending ajax request");
  $.ajax({
    type: "POST",
    url: "/lib/scripts/api/gunlist.json.php",
    data: { token: token, action : "set_description", gunlist_item_id : gunlistItemID, desc : newDescription },
    dataType: 'json',
    success: function(json){
      console.log(json);
      
    },
    failure: function(errMsg) {
      $("#"+gunlistItemID).text("ERROR");
    }
  });  

}



$('.move-up').click(function(){
  var id = $(this).data("id");

var thisRow =  $(this).closest("tr");
var prevRow =  $(this).closest("tr").prev('tr');

console.log(thisRow);
console.log(prevRow);


prevRow.insertAfter( thisRow );

  //prevRow.after(thisRow);


  console.log("sending ajax request - move up: " + id);

  $.ajax({
    type: "POST",
    url: "/lib/scripts/api/gunlist.json.php",
    data: { token: token, action : "set_sort_up", gunlist_item_id : id },
    dataType: 'json',
    success: function(json){
      console.log(json);


      

      
    },
    failure: function(errMsg) {
      //$("#"+id).text("ERROR");
      alert("failed shifting record up, refresh page!");
    }
  });  
});



$('.move-down').click(function(){
    var prevRow =  $(this).closest("tr");
    var thisRow =  $(this).closest("tr").next('tr');

    var id = thisRow.data("id");

    if(typeof id === 'undefined'){
      return;
      console.log("next row is not a valid row");
    };

    prevRow.insertAfter( thisRow );

  $.ajax({
    type: "POST",
    url: "/lib/scripts/api/gunlist.json.php",
    data: { token: token, action : "set_sort_up", gunlist_item_id : id },
    dataType: 'json',
    success: function(json){
      console.log(json);
    },
    failure: function(errMsg) {
      //$("#"+id).text("ERROR");
      alert("failed shifting record down, refresh page!");
    }
  });  


});


$('.edit-area').click(function(){

  var gunlistID = $(this).data("id");
  var armsbookNo = $(this).data("armsbookno");
  var description = $("#"+gunlistID).text();

  console.log(gunlistID);

  loadModal_area(gunlistID,armsbookNo,description);
  //show modal
  $("#modal_area").modal('show');

});


var modalGunlistID = -1;
function loadModal_area(gunlistID,armsbookNo,description){
  modalGunlistID = gunlistID;
  $("#modal_area-description").text(description);
  $("#mondal_area-bookNumber").text(armsbookNo);
}


$("#modal_area-submit").click(function(){

    var area_id = $("#modal_area-area").val();
    var gunlist_item_id = modalGunlistID;
    console.log("area: " + area_id);
    console.log("GunlistID: " + modalGunlistID);



  $.ajax({
    type: "POST",
    url: "/lib/scripts/api/gunlist.json.php",
    data: { token: token, action : "set_area", gunlist_item_id : gunlist_item_id, area_id : area_id },
    dataType: 'json',
    success: function(json){
      console.log(json);
      $("#row_"+gunlist_item_id).hide();
    },
    failure: function(errMsg) {
      //$("#"+id).text("ERROR");
    }
  });  






  $("#modal_area").modal('hide');


});




</script>


<?php
  include($_SERVER['DOCUMENT_ROOT']."/lib/template/footer.php");
?>

