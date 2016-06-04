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


/*-----------------main---------------------*/

$data['opttype']  = 22 ; //oc_option 表id


//查询属性类型
$selectopttype = "SELECT * from oc_option LEFT JOIN oc_option_description on oc_option.option_id = oc_option_description.option_id WHERE oc_option_description.language_id = 1";
$opttype = $db->query($selectopttype);


//查询属性 
$selectopt = "SELECT * FROM oc_option_value LEFT JOIN oc_option_value_description  ON oc_option_value.option_value_id=oc_option_value_description.option_value_id WHERE oc_option_value_description.language_id= 1 and oc_option_value.option_id = '".$data['opttype']."'  order by oc_option_value.sort_order asc";
$opt = $db->query($selectopt);


//查询分类
$selectcat = "select * from oc_category LEFT JOIN oc_category_description on oc_category.category_id = oc_category_description.category_id WHERE oc_category_description.language_id= 1 ";
$cat = $db->query($selectcat);

if($_POST){

 // $data['opttype'] = (int) $_POST['opttype'];
  
  if($data['opttype'] == ''){
    echo ' <script>alert("分类类别不能为空!"); window.history.back(-1); </script>';
  }
  $data['cat']  = $_POST['cat'];
  $data['opt']  = $_POST['opt'];
  $opt_price = 0 ; //属性价格
  $optid = $data['opttype'];

  $data['cat'] = explode(",", $data['cat']);
  $data['opt'] = explode(",", $data['opt']);


  foreach ($data['cat'] as $key => $value) {
      //查询该分类下的所有的产品
      $products = $db->query("SELECT * FROM oc_product_to_category WHERE category_id =".$value." GROUP BY category_id");
      foreach ($products->rows as  $product) {

      $inser_product_option = $db->query("INSERT INTO `oc_product_option` (`product_id`, `option_id`, `required`) VALUES ('".$product['product_id']."', '".$data['opttype']."', '1')");   
      $pro_optid = $db->getLastId();

      if($inser_product_option){
        foreach ($data['opt'] as $k => $v) {
           
              $product_id = $product['product_id'];
              $sql = "INSERT INTO `oc_product_option_value` (`product_option_value_id`, `product_option_id`, `product_id`, `option_id`, `option_value_id`, `quantity`, `subtract`, `price`, `price_prefix`, `points`, `points_prefix`, `weight`, `weight_prefix`) VALUES ('', '$pro_optid', '$product_id', '$optid', '$v', '999', '1', '$opt_price', '+', '0', '+', '0', '+')";
             // echo $sql."<br>";
              $product_option_value = $db->query($sql);
              if($product_option_value){
                  echo $product['product_id']."-ok<br>";
              }
           
        }
      }

      }
  }
  exit();
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>opt</title>
<link href="//cdn.bootcss.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" rel="stylesheet">
<script src="/vue.min.js"></script>
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
        <h2 >属性</h2>
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