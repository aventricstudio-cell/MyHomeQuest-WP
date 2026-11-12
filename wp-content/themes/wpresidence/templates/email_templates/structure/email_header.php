<?php 
$logo_source      =  wpresidence_get_option('wp_estate_email_logo_image', 'url');
$show_email_header=  wpresidence_get_option('wpestate_display_header_email');
$email_back       =  get_stylesheet_directory_uri().'/email_templates/images/email_back.png'; 


$wp_estate_email_background=  wpresidence_get_option('wp_estate_email_background', '');
if($wp_estate_email_background==''){
  $wp_estate_email_background='#F7FAFC';
}
$is_rtl = is_rtl() ? 'rtl' : 'ltr';

?>
<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <link href="https://fonts.googleapis.com/css?family=Roboto:wght@300;400;500;700&subset=latin,latin-ext&display=swap" rel="stylesheet">
    <title><?php print get_bloginfo('name'); ?></title>
    <!--[if mso]>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
    <style>
      table {border-collapse: collapse;}
      td,th,div,p,a {font-size: 16px; line-height: 26px;}
      .spacer,.divider,div,p,a,h1,h2,h3,h4,h5,h6 {mso-line-height-rule: exactly;}
      td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family:"Segoe UI",Helvetica,Arial,sans-serif;}
    </style>
    <![endif]-->

    <style type="text/css">
        
    html, body, div, span, object, iframe,
    h1, h2, h3, h4, h5, h6, p, blockquote, pre,
    abbr, address, cite, code,
    del, dfn, em, img, ins, kbd, q, samp,
    small, strong, sub, sup, var,
    b, i,
    dl, dt, dd, ol, ul, li,
    fieldset, form, label, legend,
    table, caption, tbody, tfoot, thead, tr, th, td,
    article, aside, canvas, details, figcaption, figure,
    footer, header, hgroup, menu, nav, section, summary,
    time, mark, audio, video {
        margin:0;
        padding:0;
        border:0;
        outline:0;
        font-size:100%;
        vertical-align:baseline;
    }

    body {
        line-height:1;
    }

    article,aside,details,figcaption,figure,
    footer,header,hgroup,menu,nav,section {
        display:block;
    }


    a {
        margin:0;
        padding:0;
        font-size:100%;
        vertical-align:baseline;
        background:transparent;
        font-weight: bold;
        color: #718096!important;
    }

    .container{
        width: 600px;
    }
    
    .wpestate_logo{
        width: 150px;
    }
    
    .wpestate_footer .source{
        font-size: 10px!important;
    }
    
    .wpestate_social_icons{
        padding: 25px 0px 0px 0px;
        text-align: center;
    }
    .wpestate_social_icons a{
        text-decoration: none;
        display: inline-block;
        width: 40px;
        height: 40px;
        text-align: center;
        vertical-align: middle;
        line-height: 39px!important;
        margin: 0px 5px;
    }
    .wpestate_social_icons svg{
        fill:#ddd;
    }
    .wpestate_social_icons img{
        width: 15px;
    }
    .wpestate_email_content_footer_new,
    .wpestate_email_content_footer{
        width: 100%;
        color: #718096;
        line-height: 17px;
        font-size: 12px;
        display: inline-block;
        
    }
    .wpestate_email_content_footer_new span{
      width:100%;
    }
    
    .wpestate_email_table_wrapper{
      background-color:#ffffff;
      padding: 48px 24px 32px 24px!important;
      box-shadow: 0 10px 31px 0 rgb(7 152 255 / 9%)!important;
      border-radius: 2px!important;
    }


      @media only screen {
        .col, td, th, div, p {font-family: -apple-system,system-ui,BlinkMacSystemFont,"Segoe UI","Roboto","Helvetica Neue",Arial,sans-serif;}
        h1{margin:20px 0px!important;color:#211465!important;font-size:36px!important;line-height:1.6em!important;}
        .source {font-family: 'Source Sans Pro', Arial, sans-serif!important;line-height: 1.5em!important;font-size:17px!important;}
      }

      #outlook a {padding: 0;}
      img {border: 0; line-height: 100%; vertical-align: middle;}
      .col {font-size: 16px; line-height: 26px; vertical-align: top;}
      .col.px-sm-16{border-radius: 10px!important;}

      @media only screen and (max-width: 730px) {
        .wrapper img {max-width: 100%;}
        u ~ div .wrapper {min-width: 100vw;}
        .container {width: 100%!important; -webkit-text-size-adjust: 100%;}
      }

      @media only screen and (max-width: 699px) {
        .col {
          box-sizing: border-box;
          display: inline-block!important;
          line-height: 23px;
          width: 100%!important;
        }

        .col-sm-1  {max-width: 8.33333%;}
        .col-sm-2  {max-width: 16.66667%;}
        .col-sm-3  {max-width: 25%;}
        .col-sm-4  {max-width: 33.33333%;}
        .col-sm-5  {max-width: 41.66667%;}
        .col-sm-6  {max-width: 50%;}
        .col-sm-7  {max-width: 58.33333%;}
        .col-sm-8  {max-width: 66.66667%;}
        .col-sm-9  {max-width: 75%;}
        .col-sm-8 {max-width: 83.33333%;}
        .col-sm-11 {max-width: 91.66667%;}

        .col-sm-push-1  {margin-left: 8.33333%;}
        .col-sm-push-2  {margin-left: 16.66667%;}
        .col-sm-push-3  {margin-left: 25%;}
        .col-sm-push-4  {margin-left: 33.33333%;}
        .col-sm-push-5  {margin-left: 41.66667%;}
        .col-sm-push-6  {margin-left: 50%;}
        .col-sm-push-7  {margin-left: 58.33333%;}
        .col-sm-push-8  {margin-left: 66.66667%;}
        .col-sm-push-9  {margin-left: 75%;}
        .col-sm-push-10 {margin-left: 83.33333%;}
        .col-sm-push-11 {margin-left: 91.66667%;}

        .full-width-sm {display: table!important; width: 100%!important;}
        .stack-sm-first {display: table-header-group!important;}
        .stack-sm-last {display: table-footer-group!important;}
        .stack-sm-top {display: table-caption!important; max-width: 100%; padding-left: 0!important;}

        .toggle-content {
          max-height: 0;
          overflow: auto;
          transition: max-height .4s linear;
            -webkit-transition: max-height .4s linear;
        }
        .toggle-trigger:hover + .toggle-content,
        .toggle-content:hover {max-height: 999px!important;}

        .show-sm {
          display: inherit!important;
          font-size: inherit!important;
          line-height: inherit!important;
          max-height: none!important;
        }
        .hide-sm {display: none!important;}

        .align-sm-center {
          display: table!important;
          float: none;
          margin-left: auto!important;
          margin-right: auto!important;
        }
        .align-sm-left {float: left;}
        .align-sm-right {float: right;}

        .text-sm-center {text-align: center!important;}
        .text-sm-left   {text-align: left!important;}
        .text-sm-right  {text-align: right!important;}

        .borderless-sm {border: none!important;}
        .nav-sm-vertical .nav-item {display: block;}
        .nav-sm-vertical .nav-item a {display: inline-block; padding: 4px 0!important;}

        .spacer {height: 0;}

        .p-sm-0 {padding: 0!important;}
        .p-sm-8 {padding: 8px!important;}
        .p-sm-16 {padding: 16px!important;}
        .p-sm-24 {padding: 24px!important;}
        .p-sm-32 {padding: 32px!important;}
        .pt-sm-0 {padding-top: 0!important;}
        .pt-sm-8 {padding-top: 8px!important;}
        .pt-sm-16 {padding-top: 16px!important;}
        .pt-sm-24 {padding-top: 24px!important;}
        .pt-sm-32 {padding-top: 32px!important;}
        .pr-sm-0 {padding-right: 0!important;}
        .pr-sm-8 {padding-right: 8px!important;}
        .pr-sm-16 {padding-right: 16px!important;}
        .pr-sm-24 {padding-right: 24px!important;}
        .pr-sm-32 {padding-right: 32px!important;}
        .pb-sm-0 {padding-bottom: 0!important;}
        .pb-sm-8 {padding-bottom: 8px!important;}
        .pb-sm-16 {padding-bottom: 16px!important;}
        .pb-sm-24 {padding-bottom: 24px!important;}
        .pb-sm-32 {padding-bottom: 32px!important;}
        .pl-sm-0 {padding-left: 0!important;}
        .pl-sm-8 {padding-left: 8px!important;}
        .pl-sm-16 {padding-left: 16px!important;}
        .pl-sm-24 {padding-left: 24px!important;}
        .pl-sm-32 {padding-left: 32px!important;}
        .px-sm-0 {padding-right: 0!important; padding-left: 0!important;}
        .px-sm-8 {padding-right: 8px!important; padding-left: 8px!important;}
        .px-sm-16 {padding-right: 16px!important; padding-left: 16px!important;}
        .px-sm-24 {padding-right: 24px!important; padding-left: 24px!important;}
        .px-sm-32 {padding-right: 32px!important; padding-left: 32px!important;}
        .py-sm-0 {padding-top: 0!important; padding-bottom: 0!important;}
        .py-sm-8 {padding-top: 8px!important; padding-bottom: 8px!important;}
        .py-sm-16 {padding-top: 16px!important; padding-bottom: 16px!important;}
        .py-sm-24 {padding-top: 24px!important; padding-bottom: 24px!important;}
        .py-sm-32 {padding-top: 32px!important; padding-bottom: 32px!important;}
        .chrink{font-size: 90px !important; line-height: 90px;}
      }
    .rtl {
        direction: rtl;
        text-align: right;
    }

    .ltr {
        direction: ltr;
        text-align: left;
    }

    /* Add any specific styles for RTL */
    .rtl .wpestate_logo {
        float: right;
    }

    .rtl .wpestate_social_icons {
        text-align: left;
    }

    .ltr .wpestate_logo {
        float: left;
    }

    .ltr .wpestate_social_icons {
        text-align: right;
    }

    @media only screen and (max-width: 699px) {
        .rtl .col, .ltr .col {
            width: 100%!important;
            text-align: center!important;
        }
    }

    </style>
  </head>
  <body class="<?php echo esc_attr($is_rtl); ?>"  style="box-sizing:border-box;margin:0;padding:0;width:100%;word-break:break-word;-webkit-font-smoothing:antialiased;">


    <div style="display:none;font-size:0;line-height:0;"><!-- Add your preheader text here --></div>

    <!-- Email Wrapper -->
    <table class="wrapper" cellpadding="15" cellspacing="0" role="presentation" width="100%">
      <tr>
        <td align="center" bgcolor="<?php echo esc_attr($wp_estate_email_background);?>" >
          <!-- 400px Container -->
          <table class="container" cellpadding="0" cellspacing="0" role="presentation" width="500">
            <tr>
              <td align="left" class="px-sm-16 py-sm-24"  style="padding: 24px 0;">
                <!-- section : Logo -->

                <?php if($show_email_header=='yes'){ ?>
                  <table cellpadding="0" cellspacing="0" role="presentation" width="100%">
                    <tr>
                      <td style="padding: 0px;">
                        <table cellpadding="0" cellspacing="0" role="presentation" width="100%">
                          <tr>
                            <td class="col px-sm-16" align="center" width="352" style="padding: 32px 24px;">
                              <img mc:edit='Logo' src="<?php print esc_attr($logo_source); ?>" alt="Logo" class="wpestate_logo">

                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                <?php } ?>
                <!-- End Logo -->

                <!--  ----------------------------------------------------------------------
                  Copy the Template below
                ------------------------------------------------------------------------- -->  

                  
                  
                  
                  
                  
                  