<?php
// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$registry->set('db', $db);


//查询属性 插入
$selectopt = "SELECT * FROM oc_option_value LEFT JOIN oc_option_value_description  ON oc_option_value.option_value_id=oc_option_value_description.option_value_id WHERE oc_option_value_description.language_id= 1";
$opt = $db->query($selectopt);


//查询分类
$selectcat = "select * from oc_category LEFT JOIN oc_category_description on oc_category.category_id = oc_category_description.category_id WHERE oc_category_description.language_id= 1 ";
$cat = $db->query($selectcat);

if($_POST){
  $data['cat']  = $_POST['cat'];
  $data['cat'] = explode(",", $data['cat']);
  $data['opt'] = explode(",", $data['opt']);


  foreach ($data['cat'] as $key => $value) {
      foreach ($data['opt'] as $k => $v) {
           
      }
  }
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>opt</title>
<link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" rel="stylesheet">
<script src="//cdn.bootcss.com/vue/1.0.24/vue.common.js"></script>
<script>
new Vue({
  el: '#cat',
  data: {
    selected: []
  }
})
</script>
<style>
.select{
	min-height:300px !important;
}
</style>
</head>

<body>
<div class="container" style="margin-top:8em;">
  <div class="row clearfix">
    <div class="col-md-5 column">
      <form action="" method="post">
        <h2>分类</h2>
        <hr>
        <div id="cat" class="demo">
          <select v-model="selected" multiple class="form-control select">
            <option v-for="option in options" v-bind:value="option.value">
      {{ option.text }}
            </option>
          </select>
          <input name="cat" type="hidden" value="{{ selected }}">
        </div>
        <script>
new Vue({
  el: '#cat',
  data: {
    selected: '',
    options: [
<?php
    foreach ($cat->rows as $key => $value) {
      echo '{ text: "'.trim($value['name']).'", value: "'.$value['category_id'].'" },';
    }
   ?>
    ]
  }
})
        </script>
      </div>
      <div class="col-md-5 column">
        <h2>属性</h2>
        <hr>
        <div id="opt" class="demo">
          <select v-model="selected" multiple class="form-control select">
            <option v-for="option in options" v-bind:value="option.value">
      {{ option.text }}
            </option>
          </select>
          <input name="opt" type="hidden" value="{{ selected }}">
        </div>
        <script>
new Vue({
  el: '#opt',
  data: {
    selected: '',
    options: [
<?php
    foreach ($opt->rows as $key => $value) {
      echo '{ text: "'.trim($value['name']).'", value: "'.$value['option_value_id'].'" },';
    }
   ?>
    ]
  }
})
        </script>
      </div>
      <br>
    </div>
    <button type="submit" class="btn btn-primary">插入</button>
  </form>
</div>

</body>
</html>