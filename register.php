<?php include_once("header.php");?>
<div class="container">
  <h2 class="my-3">Register new account</h2>
  <!-- Register User Form -->
  <!-- Navigate to reigster_user.php to deal register user into the database -->
  <form method="POST" action="register_user.php">
    <p style='color:red;'><span class="error">* required field</span></p>
    <div class="form-group row">
      <label for="FirstName" class="col-sm-3 col-form-label text-right">First Name <span class="text-danger">*</span> </label>
      <div class="col-sm-6">
        <input type="text" class="form-control" name="FirstName" required>
      </div>
    </div>
    <div class="form-group row">
      <label for="LastName" class="col-sm-3 col-form-label text-right">Last Name <span class="text-danger">*</span> </label>
      <div class="col-sm-6">
        <input type="text" class="form-control" name="LastName" required>
      </div>
    </div>
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
    <div class="form-group row">
      <label for="passwordConfirmation" class="col-sm-3 col-form-label text-right">Repeat password <span class="text-danger">*</span></label>
      <div class="col-sm-6">
        <input type="Password" class="form-control" name="passwordConfirmation" placeholder="Enter password again" required>
      </div>
    </div>
    <div class="form-group row">
      <button type="submit" class="btn btn-primary form-control">Register</button>
    </div>
  </form>
  <!-- Hyperlink to login.php if user want to login instead of register -->
  <div class="text-center">Already have an account? <a href="login.php">Login</a></div>
</div>
<?php include_once("footer.php")?>