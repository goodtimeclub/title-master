<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=title_master'.AMP.'method=settings'.AMP.'cid='.$cid);?>

<?php 
$this->table->set_template($cp_pad_table_template);

foreach ($settings as $channel => $data)
{


	//Set Fields
	$this->table->set_heading("$channel Fields",'');				

	if(!empty($fields)){

		foreach($fields as $field){
		$this->table->add_row('<a class="fieldLink">{' . $field['field_name'] . '}</a>', ucwords($field['field_type']));				
		}			
	}

	$this->table->add_row('<a class="fieldLink">{x}</a>', 'Increment');				

	echo $this->table->generate();
	 $this->table->clear();


	

	$channel_name = $data['channel_name'];
	foreach($data as $key => $value){
		$fieldName = $channel_name.'['.$key.']';
		if($key == 'channel'){
			$this->table->set_heading($channel,'');			
		}elseif($key == 'channel_id'){
			echo '<input type="hidden" name="'.$fieldName.'" value="'.$value.'" />'; 
		}elseif($key == 'channel_name'){			
		}elseif($key == 'update_url'){			
		  $yesChecked = '';
		  $noChecked = '';
		  if($value == 1){
		    $yesChecked = "checked";
		  }else{
		    $noChecked = "checked";
		  }
			$this->table->add_row(lang($key),
			  '<input type="radio" name="'.$fieldName.'" value="1" id="updateUrlYes" '.$yesChecked.'/> <label for="updateUrlYes">Yes</label>
			  &nbsp;&nbsp;&nbsp;&nbsp;
			  <input type="radio" name="'.$fieldName.'" value="0"  id="updateUrlNO" '.$noChecked.' /> <label for="updateUrlNo">No</label>');
		}elseif($key == 'update_structure'){			
		  $yesChecked = '';
		  $noChecked = '';
		  if($value == 1){
		    $yesChecked = "checked";
		  }else{
		    $noChecked = "checked";
		  }
			$this->table->add_row(lang($key),
			  '<input type="radio" name="'.$fieldName.'" value="1" id="updateStructureYes" '.$yesChecked.'/> <label for="updateStructureYes">Yes</label>
			  &nbsp;&nbsp;&nbsp;&nbsp;
			  <input type="radio" name="'.$fieldName.'" value="0"  id="updateStructureNO" '.$noChecked.' /> <label for="updateStructureNo">No</label>');
		}else{
			$this->table->add_row(lang($key),form_input($fieldName,$value,'size="50"'));
		}
	}
}

echo $this->table->generate();

?>
<h4>What would you like to do to existing entries? </h4>
<p>
  <input type="radio" name="existing" value="leave" id="leaveExisting" checked> 
  <label for="leaveExisting"><strong>Nothing!</strong> Leave them alone.</label>
</p>
<p>
  <input type="radio" name="existing" value="titles" id="titlesExisting">
  <label for="titlesExisting"> Update the <strong>Titles</strong>, but don't touch the URL Titles. </label>
</p>  
<p>
  <input type="radio" name="existing" value="both"  id="bothExisting">
  <label for="bothExisting">Update both <strong>Titles</strong> and <strong>URL Titles.</strong> (Careful, might break some links!)</label>
</p>    

<p><?=form_submit('submit', lang('submit'), 'class="submit"')?> &nbsp;
<?=form_submit('submit', 'Submit and Finished', 'class="submit"')?></p>


<?php $this->table->clear()?>
<?=form_close()?>

<script>/*
 *
 * Copyright (c) 2010 C. F., Wong (<a href="http://cloudgen.w0ng.hk">Cloudgen Examplet Store</a>)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
﻿(function(k,e,i,j){k.fn.caret=function(b,l){var a,c,f=this[0],d=k.browser.msie;if(typeof b==="object"&&typeof b.start==="number"&&typeof b.end==="number"){a=b.start;c=b.end}else if(typeof b==="number"&&typeof l==="number"){a=b;c=l}else if(typeof b==="string")if((a=f.value.indexOf(b))>-1)c=a+b[e];else a=null;else if(Object.prototype.toString.call(b)==="[object RegExp]"){b=b.exec(f.value);if(b!=null){a=b.index;c=a+b[0][e]}}if(typeof a!="undefined"){if(d){d=this[0].createTextRange();d.collapse(true);
d.moveStart("character",a);d.moveEnd("character",c-a);d.select()}else{this[0].selectionStart=a;this[0].selectionEnd=c}this[0].focus();return this}else{if(d){c=document.selection;if(this[0].tagName.toLowerCase()!="textarea"){d=this.val();a=c[i]()[j]();a.moveEnd("character",d[e]);var g=a.text==""?d[e]:d.lastIndexOf(a.text);a=c[i]()[j]();a.moveStart("character",-d[e]);var h=a.text[e]}else{a=c[i]();c=a[j]();c.moveToElementText(this[0]);c.setEndPoint("EndToEnd",a);g=c.text[e]-a.text[e];h=g+a.text[e]}}else{g=
f.selectionStart;h=f.selectionEnd}a=f.value.substring(g,h);return{start:g,end:h,text:a,replace:function(m){return f.value.substring(0,g)+m+f.value.substring(h,f.value[e])}}}}})(jQuery,"length","createRange","duplicate");

$(function(){
var curInput = $(".mainTable input").first();
var curStart = 0;
var curEnd = 0;

curInput.focus();

$("input").bind('click keyup',function () {
curInput = $(this);
curStart = $(this).caret().start;
curEnd = $(this).caret().end;
});
$("a.fieldLink").click(function(){
newVal = curInput.val().substring(0,curStart) +  $(this).text() + curInput.val().substring(curEnd);
curInput.val(newVal);
curStart = curEnd = curStart + $(this).text().length;
curInput.focus();
});

});
</script>
<style>
.fieldLink{
	font-weight: bold;
}
.fieldLink:hover{
	font-weight: bold;
	cursor: pointer;
	color:#5F6C74;
}
</style>