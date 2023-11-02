@php
    if(isset($_GET['utm_source'])) setcookie("utm_source",$_GET["utm_source"],time()+3600+24+30,"/");
    if(isset($_GET['utm_content'])) setcookie("utm_content",$_GET["utm_content"],time()+3600+24+30,"/");
    if(isset($_GET['utm_medium'])) setcookie("utm_medium",$_GET["utm_medium"],time()+3600+24+30,"/");
    if(isset($_GET['utm_campaign'])) setcookie("utm_campaign",$_GET["utm_campaign"],time()+3600+24+30,"/");
    if(isset($_GET['utm_term'])) setcookie("utm_term",$_GET["utm_term"],time()+3600+24+30,"/");
@endphp
