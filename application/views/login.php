<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Welcome to AppBooking</title>
        <link rel="stylesheet" href="css/vendors/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">        
        <link rel="stylesheet" href="css/login.css">
    </head>
    <body>           
        <div id="login">           
            <div class="container"> 
                <div class="inner">
                    <header>
                        <h1>AppBooking</h1>     
                    </header>                   
                    <?php echo form_open('/login/submit', array('role' => 'form')); ?>    
                        <div class="form-group">
                            <label for="loginEmail">Email address</label>
                            <input type="email" class="form-control" id="loginEmail" placeholder="Enter email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="loginPassword">Password</label>
                            <input type="password" class="form-control" id="loginPassword" placeholder="Password" name="password" required>
                        </div>
                        <div class="form-group center">  
                            <button type="submit" class="button btn btn-default">Submit</button>
                        </div>
                    </form>                     
                </div>                                        
                <footer>
                    &copy; 2014 by Quentin THAI. Alpha Version. Mockup in progress
                </footer>                
            </div>
        </div>

        <script src="js/vendors/jquery-2.1.0.min.js"></script>
        <script src="js/vendors/bootstrap.min.js"></script>       
        <script src="js/main.js"></script>            
    </body>
</html>