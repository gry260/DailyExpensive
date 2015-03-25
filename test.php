<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="UTF-8">
  <title>AdminLTE 2 | Dashboard</title>
  <link rel="stylesheet" href="dist/css/jquery.multiselect.css"  type="text/css" />
  <link rel="stylesheet" href="dist/css/prettify.css"  type="text/css" />
  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
  <script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
  <script src="dist/js/jquery.multiselect.js"></script>

  <script type="text/javascript">
    $(function(){
      $("select").multiselect();
    });
  </script>


</head>
<body>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <select name="example-optgroup" id="cate_sub_types" multiple="multiple" size="5">
      <optgroup label="Group One">
        <option value="option1">Option 1</option>
        <option value="option2">Option 2</option>
        <option value="option3">Option 3</option>
      </optgroup>
      <optgroup label="Group Two">
        <option value="option4">Option 4</option>
        <option value="option5">Option 5</option>
        <option value="option6">Option 6</option>
        <option value="option7">Option 7</option>
      </optgroup>
    </select>
  </section>
</div>





</body>
</html>




