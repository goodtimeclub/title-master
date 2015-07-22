
<?php

$this->EE =& get_instance();

$channel_id = $_GET['channel_id'];
$url_len = 0;

$sql = "SELECT * FROM exp_title_master WHERE channel_id='$channel_id'";
$result = $this->EE->db->query($sql);

if ($result->num_rows() > 0)
		{          
  $script = "";
  $style = "";

    
    

      if($result->row('title_tmpl') != "" AND $result->row('title_tmpl') != "{title}"){
		    $style .= '
		    #hold_field_title{
		      height:0;
		      padding:0;
		      border:0;
		      overflow:hidden;
		    }';
        $script .= 'if($("input[name=title]").val() == ""){
                $("input[name=title]").val("Autofill Title");
              };';
      }
      
      
      if(($result->row('url_title_tmpl') != "" AND $result->row('url_title_tmpl') != "{url_title}") OR ($result->row('title_tmpl') != "" AND  $result->row('title_tmpl') != "{title}") ){
		    if($result->row('update_url') > 0 OR empty($_GET['entry_id'])){
  		    $style .=  '
  		    #hold_field_url_title{
  		      height:0;
  		      padding:0;
  		      border:0;
  		      overflow:hidden;
  		    }';

            $script .= 'if($("input[name=url_title]").val() == ""){
              $("#hold_field_url_title").hide();
              $("input[name=url_title]").val("autofill_url_title");
            }';
		    }
      }

      //Separate it out In case the Update URL Conditional Needs this to be set as well
        $script .= '$("#title").attr("maxlength",'.$result->row('title_len').');';      
        $script .= '$("#url_title").attr("maxlength",'.$result->row('url_title_len').');';      

        $url_len = $result->row('url_title_len');

        $script .= '
        $(function(){
        (function(e){EE.namespace("EE.publish");e.fn.ee_url_title=function(h,f){return this.each(function(){var b=EE.publish.default_entry_title?EE.publish.default_entry_title:"",c=EE.publish.word_separator?EE.publish.word_separator:"_",i=EE.publish.foreignChars?EE.publish.foreignChars:{},a=e(this).val()||"",j=RegExp(c+"{2,}","g"),d=c!=="_"?/\_/g:/\-/g,g="",k=EE.publish.url_title_prefix?EE.publish.url_title_prefix:"";typeof f!=="boolean"&&(f=!1);b!==""&&e(this).attr("id")==="title"&&a.substr(0,b.length)===
        b&&(a=a.substr(b.length));a=(k+a).toLowerCase().replace(d,c);for(b=0;b<a.length;b++)d=a.charCodeAt(b),d>=32&&d<128?g+=a.charAt(b):d in i&&(g+=i[d]);a=g.replace("/<(.*?)>/g","");a=a.replace(/\s+/g,c);a=a.replace(/\//g,c);a=a.replace(/[^a-z0-9\-\._]/g,"");a=a.replace(/\+/g,c);a=a.replace(j,c);a=a.replace(/^[\-\_]|[\-\_]$/g,"");a=a.replace(/\.+$/g,"");f&&(a=a.replace(/\./g,""));h&&e(h).val(a.substring(0,' . $url_len . '))})}})(jQuery);
        })';


       echo "<script> \n $script \n </script>\n";
       echo "<style> \n $style \n </style> \n ";
		}

?>

<script> 
       
    $("li").has(".title_master").hide();
    $("#title_master").hide();		
		</script>