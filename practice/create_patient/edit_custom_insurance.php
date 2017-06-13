<?php
require_once("../verify_session.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style>
        body{
            overflow: hidden;
        }
       .page-content > .row{
            margin-left:0px;
            margin-right:0px;
            min-height: 100%;
            margin-bottom: -45px;
        }

        #wrapper {
            padding-left: 0px;
            transition: all .4s ease 0s;
            height: 100%;
            height: 100%
        }
        .page-content{
            min-height: 100%;
        }
        #page-content-wrapper {
            padding-left: 0;
            margin-left: 0;
            width: 100%;
            min-height: 100%;
            height: auto;
            margin-bottom: -45px;
        }
        #page-content-wrapper {
          width: 100%;
          min-height: 100%;
        }
        
        .content-header {
          height: 65px;
          line-height: 65px;
        }

        .content-header h1 {
          margin: 0;
          margin-left: 20px;
          line-height: 65px;
          display: inline-block;
        }
        #content {
            position: relative;
        }
         #loader{
            background: rgba(0,0,0,0.56);
            border-radius: 4px;
            display:table;
            height: 48px;
            width: 242px;
            color: #fff;
            position: absolute;
            left: 0px;
            top:0px;
            bottom: 0px;
            right: 0px;
            margin: auto;
            display: none;
        }
        .ajax-spinner-bars {
            height: 48px;
            left: 23px;
            position: relative;
            top: 20px;
            width: 35px;
            display: table-cell;
         }
        #loadertitle {
            display: table-cell;
            font-size: 17px;
            padding-left: 14px;
            vertical-align: middle;
         }

        .ajax-spinner-bars > div {
            position: absolute;
            width: 2px;
            height: 8px;
            background-color: #fff;
            opacity: 0.05;
            animation: fadeit 0.8s linear infinite;
        }
        .ajax-spinner-bars > .bar-1 {
        transform: rotate(0deg) translate(0, -12px);
        animation-delay:0.05s;
        }
        .ajax-spinner-bars > .bar-2 {
        transform: rotate(22.5deg) translate(0, -12px);
        animation-delay:0.1s;
        }
        .ajax-spinner-bars > .bar-3 {
        transform: rotate(45deg) translate(0, -12px);
        animation-delay:0.15s;
        }
        .ajax-spinner-bars > .bar-4 {
        transform: rotate(67.5deg) translate(0, -12px);
        animation-delay:0.2s;
        }
        .ajax-spinner-bars > .bar-5 {
        transform: rotate(90deg) translate(0, -12px);
        animation-delay:0.25s;
        }
        .ajax-spinner-bars > .bar-6 {
        transform: rotate(112.5deg) translate(0, -12px);
        animation-delay:0.3s;
        }
        .ajax-spinner-bars > .bar-7 {
        transform: rotate(135deg) translate(0, -12px);
        animation-delay:0.35s;
        }
        .ajax-spinner-bars > .bar-8 {
        transform: rotate(157.5deg) translate(0, -12px);
        animation-delay:0.4s;
        }
        .ajax-spinner-bars > .bar-9 {
        transform: rotate(180deg) translate(0, -12px);
        animation-delay:0.45s;
        }
        .ajax-spinner-bars > .bar-10 {
        transform: rotate(202.5deg) translate(0, -12px);
        animation-delay:0.5s;
        }
        .ajax-spinner-bars > .bar-11 {
        transform: rotate(225deg) translate(0, -12px);
        animation-delay:0.55s;
        }
        .ajax-spinner-bars > .bar-12 {
        transform: rotate(247.5deg) translate(0, -12px);
        animation-delay:0.6s;
        }
        .ajax-spinner-bars> .bar-13 {
        transform: rotate(270deg) translate(0, -12px);
        animation-delay:0.65s;
        }
        .ajax-spinner-bars > .bar-14 {
        transform: rotate(292.5deg) translate(0, -12px);
        animation-delay:0.7s;
        }
        .ajax-spinner-bars > .bar-15 {
        transform: rotate(315deg) translate(0, -12px);
        animation-delay:0.75s;
        }
        .ajax-spinner-bars> .bar-16 {
        transform: rotate(337.5deg) translate(0, -12px);
        animation-delay:0.8s;
        }
        .editapp,.eligibility,.demographics{
            cursor: pointer;
            position: relative;
        }

        .createapp,.editins,.Benefits,.copay,.blance{
            cursor: pointer;
        }
        .copay,.blance,.address{
            position: absolute;
            right: 30px;
            text-align: right;
        }

        @keyframes fadeit{
              0%{ opacity:1; }
              100%{ opacity:0;}
        }
                
        #iframecontenar{
            border: 0px;
        }
        #loadertitle{
            text-transform: capitalize;
        }
  </style>
  <script>
        $(document).ready(function() {
            $('#loader').show();
            $('#loader #loadertitle').html("Insurance data Loading...")
            $("#iframecontenar").attr("height",window.parent.$("iframe").height());
        });
       
        function showloader(){
            $('#loader').hide();
        }
  </script>
</head>
<body>

<div id="page-content-wrapper">
    <div class="page-content inset">
            <div class="row">
                    <div id="content">
                        <div id="loader">
                            <div class="ajax-spinner-bars">
                                <div class="bar-1"></div>
                                <div class="bar-2"></div>
                                <div class="bar-3"></div>
                                <div class="bar-4"></div>
                                <div class="bar-5"></div>
                                <div class="bar-6"></div>
                                <div class="bar-7"></div>
                                <div class="bar-8"></div>
                                <div class="bar-9"></div>
                                <div class="bar-10"></div>
                                <div class="bar-11"></div>
                                <div class="bar-12"></div>
                                <div class="bar-13"></div>
                                <div class="bar-14"></div>
                                <div class="bar-15"></div>
                                <div class="bar-16"></div>
                            </div>
                            <div id="loadertitle">Insurance Loading...</div>
                        </div>
                        <iframe id="iframecontenar" class="help-contentFrame" src="insurance_edit.php?provider=<?php echo $provider; ?>&pid=<?php echo $pid; ?>&refer=<?php echo $refer; ?>" onload="showloader()" height="100%" width="100%"></iframe>
                    </div>
            </div>
    </div>
</div>        

</body>
</html>

