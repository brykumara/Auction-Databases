<?php 
  include_once("header.php");
  error_reporting(E_ERROR | E_PARSE);
?>

<div class="container">
  <h2 class="my-3">Login Here</h2>
  <!-- Log in form -->
  <!-- Navigate to login_result.php to deal with submission -->
  <form method="POST" action="login_result.php">
    <p style='color:red;'><span class="error">* required field</span></p>
    <div class="form-group row">
      <label for="Email" class="col-sm-3 col-form-label text-right">Email <span class="text-danger">*</span></label>
    <div class="col-sm-6">
        <input type="text" class="form-control" name="Email" required>
    </div>
    </div>
    <div class="form-group row">
      <label for="Password" class="col-sm-3 col-form-label text-right">Password <span class="text-danger">*</span></label>
      <div class="col-sm-6">
        <input type="Password" class="form-control" name="Password" required>
      </div>
    </div>
    <!-- Submit button -->
    <div class="form-group row">
      <button type="submit" class="btn btn-primary form-control">Login</button>
    </div>
  </form>
  <!-- Hyperlink to register.php if user want to register instead of login -->
  <div class="text-center">or <a href="register.php">create an account</a></div>
</div>

<?php include_once("footer.php")?>