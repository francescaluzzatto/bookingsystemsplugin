
    //add new time slot 
    function addnewTimeSlot(){
        alert("New Time Slot added!");
        const timeSlot= document.getElementById("timeSlot");
        const inputContainer = document.getElementById("inputContainer");
        function addInputLine(){
            //Create two new time inputs 
            const newInput1 = document.createElement('input');
            newInput1.type="time";
            newInput1.name= 'timeslot[]';
            const newInput2= document.createElement('input');
            newInput2.type= "time";
            newInput2.name= "timeslot[]";
            //create - seperator
            const separator= document.createTextNode(' - ')
            // create <br>
            const linebreak= document.createElement('br');
            //add into inputContainer
            inputContainer.appendChild(newInput1);
            inputContainer.appendChild(separator);
            inputContainer.appendChild(newInput2);
            inputContainer.appendChild(linebreak)
        }
        addInputLine();
    
    }
        //add new Except Date 
        function addnewExceptDate(){
        alert("New Except Date added!");
        const exceptDate= document.getElementById("exceptDate");
        const inputContainer2 = document.getElementById("inputContainer2");
        function addInputLine(){
            //Create two new time inputs 
            const newInput = document.createElement('input');
            newInput.type="date";
            newInput.name= 'except_date[]';
            // create <br>
            const linebreak= document.createElement('br');
            //newInput.after(timeSlot);
            //Create select daily, weekly, monthly button
            const select= document.createElement("select");
            select.id= "frequency";
            select.name = "frequency[]";
    
            // Create the options
            const optionDaily = document.createElement("option");
            optionDaily.value = "daily";
            optionDaily.textContent = "Daily";
        
            const optionWeekly = document.createElement("option");
            optionWeekly.value = "weekly";
            optionWeekly.textContent = "Weekly";
        
            const optionMonthly = document.createElement("option");
            optionMonthly.value = "monthly";
            optionMonthly.textContent = "Monthly";
    
            const optionNone= document.createElement("option");
            optionNone.value= "none";
            optionNone.textContent= "none";
    
            const repeat= document.createElement("label");
            repeat.textContent= "Repeat: ";
        
            // Append options to the select element
            select.appendChild(optionDaily);
            select.appendChild(optionWeekly);
            select.appendChild(optionMonthly);
            select.appendChild(optionNone);
            inputContainer2.appendChild(newInput);
            inputContainer2.appendChild(repeat);
            inputContainer2.appendChild(select);
        }
        addInputLine();
    
    }

