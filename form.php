<?php
    include 'top.php';
    
    $dataIsGood = false;
    $firstname = '';
    $lastname = '';
    $email = '';
    $valuable = '';
    $creative = false;
    $open = false;
    $educated = false;
    $traditional = false;
    $stubborn = false;
    $medium = '';
    $comments = '';
    
    //Sanitizing data
    function getData($field) {
    if (!isset($_POST[$field])) {
        $data = "";
    } 
    else {
        $data = trim($_POST[field]);
        $data = htmlspecialchars($data);
    }
    return $data;
    }
    
    function verifyAlphaNum($testString) {
        return (preg_match ("/^([[:alnum:]]|-|\.| |'|&\;|#)+$/", $testString));
    }
?>
        
        <section>
            <h2>Questionnaire</h2>
            <?php
            print '<p>Post Array:</p><pre>';
            print_r($_POST);
            print '</pre>';
            
            //process form when it is submitted
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                //server side sanitization
                $dataIsGood = true;
                
                $firstname = getData("txtFirstName");
                
                $lastname = getData("txtLastName");
                
                $email = getData("txtEmail");
                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                
                $valuable = getData("radFirst");
                
                $creative = (int) getData("chkCreative");
                $open = (int) getData("chkOpen");
                $educated = (int) getData("chkEducated");
                $traditional = (int) getData("chkTraditional");
                $stubborn = (int) getData("chkStubborn");
                
                $medium = getData("lstMedium");
                
                $comments = getData("txtTell");
            
                
                //server side validation
                if($firstname == "") {
                    print '<p class ="mistake">Please enter your first name.</p>';
                    $dataIsGood = false;
                }
                elseif(!verifyAlphaNum($firstname))
                {
                    print '<p class ="mistake">That first name is invalid.</p>';
                    $dataIsGood = false;
                }
                
                if($lastname == "") {
                    print '<p class ="mistake">Please enter your last name.</p>';
                    $dataIsGood = false;
                }
                elseif(!verifyAlphaNum($lastname))
                {
                    print '<p class ="mistake">That last name is invalid.</p>';
                    $dataIsGood = false;
                }
                
                if($email == "") {
                    print '<p class ="mistake">Please enter your email address.</p>';
                    $dataIsGood = false;
                }
                elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    print '<p class ="mistake">Your email address appears to be invalid.</p>';
                    $dataIsGood = false;
                }
                
                //Since this area isn't required, I added a clause that allows the statement not to perform if a blank space is selected, ie none
                if($valuable != 'Yes' AND $valuable != 'Somewhat' AND $valuable != 'No' AND $valuable != '') {
                    print '<p class ="mistake">Please select how an option to represent how valuable you think art is.</p>';
                    $dataIsGood = false;
                }
                
                $totalChecked = 0;
                
                if($creative != 1) {$creative = 0;}
                $totalChecked += $creative;
                if($open != 1) {$open = 0;}
                $totalChecked += $open;
                if($stubborn != 1) {$stubborn = 0;}
                $totalChecked += $stubborn;
                if($educated != 1) {$educated = 0;}
                $totalChecked += $eduacted;
                if($traditional != 1) {$traditional = 0;}
                $totalChecked += $traditional;
                //I'm not checking if $totalChecked is zero because it's OK if the user doesn't choose any options
                
                
                if($medium != 'Visual Art' AND $medium != 'Literature' AND $medium != 'Cinema' AND $medium != 'Music' AND $medium != 'Video Games') {
                    print '<p class ="mistake">Please select your favorite artistic medium.</p>';
                    $dataIsGood = false;
                }
                
                if(!verifyAlphaNum($comments))
                {
                    print '<p class ="mistake">The comments were invalid.</p>';
                    $dataIsGood = false;
                }
                
                
                //save the data
                if($dataIsGood){
                    try{
                        $sql = 'INSERT INTO tblSurveyData(fldFirstName, fldLastName, fldEMail, fldValuable, fldCreative, fldOpen, fldEducated, fldTraditional, fldStubborn, fldMedium, fldComments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                        $statement = $pdo->prepare($sql);
                        $params = array($firstname, $lastname, $email, $valuable, $creative, $open, $educated, $traditional, $stubborn, $medium, $comments);
                        
                        if($statement->execute($params)){
                            print '<p>Record was successfully saved.</p>';
                        }
                        else {
                            print '<p>Record was NOT successfully saved.</p>';
                        }
                    } catch (PDOException $e) {
                        print '<p>Couldn\'t insert the record, please contact me @hwchugh@gmail.com</p>';
                    }//ends try
                }//ends data is good
            }//ends form was submitted    
            if($dataIsGood) {
                print '<h2>Thank you, your information has been submitted.</h2>';
            }
            
            
            ?>
            
            <figure class = "floatright">
            <img class = "rounded" alt="Painter's Pallette" src="images/paint-pallette.png">
            <figcaption><cite>Public domain clip art of a Painter's palette.</cite></figcaption>
            </figure>
            <form action = "#"
                  method ="POST">

                <fieldset class = "contact">
                    <legend>Who are You?</legend>
                    <p>
                        <label class = "required" for = "txtFirstName">First Name</label>
                        <input id = "txtFirstName"     
                               name = "txtFirstName"
                               tabindex = "100"
                               type = "text"
                               value = "<?php print $firstname; ?>"
                               required>
                    </p>   

                    <p>
                        <label class = "required" for = "txtLastName">Last Name</label>
                        <input id = "txtLastName"     
                               name = "txtLastName"
                               tabindex = "100"
                               type = "text"
                               value = "<?php print $lastname; ?>"
                               required>
                    </p>
                    
                    <p>
                        <label class = "required" for = "txtEmail">Email</label>
                        <input id = "txtEmail"     
                               name = "txtEmail"
                               tabindex = "100"
                               type = "text"
                               value = "<?php print $email; ?>"
                               required>
                    </p> 
                </fieldset>

                <fieldset class="radio">
                    <legend>Do you think art is valuable?</legend>
                    <p>
                        <input type="radio" 
                               id="radFirstYes" 
                               name="radFirst" 
                               <?php if($valuable == 'Yes') {print 'checked';} ?>
                               value="Yes" 
                               tabindex="210" 
                               required>
                        <label class="radio-field" 
                               for = "radFirstYes">Yes</label>
                    </p>

                    <p>
                        <input type="radio" 
                               id="radFirstSomewhat" 
                               name="radFirst" 
                               <?php if($valuable == 'Somewhat') {print 'checked';} ?>
                               value="Somewhat" 
                               tabindex="210" 
                               required>
                        <label class="radio-field" 
                               for = "radFirstSomewhat"> Somewhat</label>
                    </p>

                    <p>
                        <input type="radio" 
                               id="radFirstNo" 
                               name="radFirst" 
                               <?php if($valuable == 'No') {print 'checked';} ?>
                               value="No" 
                               tabindex="210" 
                               required>
                        <label class="radio-field" 
                               for = "radFirstNo"> No</label>
                    </p>

                </fieldset>
                
                <fieldset class="checkbox">
                    <legend>What best describes you? (check all that apply):</legend>

                    <p>
                        <input
                            id="chkCreative"
                            name="chkCreative"
                            type="checkbox"
                            <?php if($creative == '1') {print 'checked';} ?>
                            value="1">
                        <label for="chkCreative">Creative</label>
                    </p>

                    <p>
                        <input
                            id="chkOpen"
                            name="chkOpen"
                            type="checkbox"
                            <?php if($open == '1') {print 'checked';} ?>
                            value="1">
                        <label for="chkOpen">Open-minded</label>
                    </p>

                    <p>
                        <input
                            id="chkEducated"
                            name="chkEducated"
                            type="checkbox"
                            <?php if($educated == '1') {print 'checked';} ?>
                            value="1">
                        <label for="chkEducated">Educated</label>
                    </p>

                    <p>
                        <input
                            id="chkTraditional"
                            name="chkTraditional"
                            type="checkbox"
                            <?php if($traditional == '1') {print 'checked';} ?>
                            value="1">
                        <label for="chkTraditional">Traditional</label>
                    </p>
                    <p>
                        <input
                            id="chkStubborn"
                            name="chkStubborn"
                            type="checkbox"
                            <?php if($stubborn == '1') {print 'checked';} ?>
                            value="1">
                        <label for="chkStubborn">Stubborn</label>
                    </p>        
                </fieldset>
                
                <fieldset  class="listbox">

                    <legend>What is your favorite artistic medium?</legend>
                    <p>
                        <select id="lstMedium" 
                                name="lstMedium" 
                                 >
                            <option <?php if($medium == 'Visual Art') {print 'selected';} ?> value="Visual Art">Visual Art</option>
                            <option <?php if($medium == 'Literature') {print 'selected';} ?>value="Literature">Literature</option>
                            <option <?php if($medium == 'Cinema') {print 'selected';} ?> value="Cinema">Cinema</option>
                            <option <?php if($medium == 'Music') {print 'selected';} ?>value="Music">Music</option>
                            <option <?php if($medium == 'Video Games') {print 'selected';} ?>value="Video Games">Video Games</option>

                        </select>
                    </p>
                </fieldset>     

                <fieldset class="textarea">
                    <legend>Anything you want to tell me?</legend>
                    <p>
                        <textarea
                            id= "txtTell"
                            name= "txtTell"
                            <?php print $comments; ?>
                        ></textarea>
                    </p>
                </fieldset>

                <fieldset class = "centercontent">
                    <p>
                        <input id="btnSubmit"
                               type ="submit">
                    </p>
                </fieldset>
            </form>
        </section>
<?php include 'footer.php'; ?>
</body>
</html>
