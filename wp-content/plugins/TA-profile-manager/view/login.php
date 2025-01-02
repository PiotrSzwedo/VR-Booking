<?php
    $language = require_once __DIR__."/../_inc/lang/pl-PL.php";
?>
<div class="ta-profile-manager-auth">
    <div class="ta-profile-manager-auth-div">
        <div class="submit-div">
            <img class="logo" src="/wp-content/plugins/TA-profile-manager/_inc/img/ta-logo.svg" alt="">
        </div>
        <form action="" method="post">
            <div class="input-div">
                <div class="form-div bg">
                    <img src="/wp-content/plugins/TA-profile-manager/_inc/img/user-icon.svg" alt="">
                    <input placeholder="<?php echo $language["name_or_email"]; ?>" name="identification" class="testable-input" type="text">
                </div>
                <div class="form-div">
                    <p class="error-field">
                        <?php echo $_GET["login_error_username"] ?>
                    </p>
                </div>
            </div>
            <div class="input-div">
                <div class="form-div bg">
                    <img src="/wp-content/plugins/TA-profile-manager/_inc/img/lock.svg" alt="">
                    <input placeholder="<?php echo $language["password"]; ?>" name="password" id="password" class="testable-input" type="password" style="border-radius: 0px;">
                    <img id="passwordImage" onclick="showPassword()" src="/wp-content/plugins/TA-profile-manager/_inc/img/eye-open-svgrepo-com.svg" alt="eye">
                </div>
                <div class="form-div">
                    <p class="error-field">
                        <?php echo $_GET["login_error_password"] ?>
                    </p>
                </div>
            </div>
            <div class="input-div form-div">
                <input name="is_remember_me" type="checkbox" name="" id="">
                <span><?php echo $language["remember_me"]; ?></span>
            </div>
            <div class="submit-div">
                <input class="submit-button" name="ta-profile-manager-login" type="submit" value="<?php echo $language["login"]; ?>">
            </div>
        </form>
    </div>
</div>
<script>
    function showPassword(){
        input = document.getElementById("password");
        image = document.getElementById("passwordImage");

        if (input.type === "password"){
            input.type = "text";
            image.src = "/wp-content/plugins/TA-profile-manager/_inc/img/low-vision.svg";
        }else{
            input.type = "password";
            image.src = "/wp-content/plugins/TA-profile-manager/_inc/img/eye-open-svgrepo-com.svg";
        }
    }
</script>