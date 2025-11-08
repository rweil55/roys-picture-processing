
var gotit = {
 gotAddr : "false",
 gotEmail : "false",
 gotQuanity : "false"
};


function emailEntered ()
{
	item = document.getElementById("emailLabel");
	var email = document.getElementById("email").value;
	if (email.length < 1)
	{
		item.style.color = "red"
		gotEmail = false;
	  	setSubmitButton();
      	return false;
	}
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   	if(reg.test(email) == false) {
		item.style.color = "red"
		gotEmail = false;
		setSubmitButton();
		return false;
	}
	item.style.color = "black"
	gotEmail = true;
	setSubmitButton();
	return true;
}
function quanityEntered (min)
{
	var item = document.getElementById("quanity");
	var quanity = item.value;
	var itemLabel = document.getElementById("quanityLabel");
	if (quanity.length < 1)
	{
		itemLabel.innerHTML = "Missing Quanity";
		itemLabel.style.color = "red"
		gotQuanity = false;
	  	setSubmitButton();
      	return false;
	}
	var reg = /^[0-9]*$/;
   	if(reg.test(quanity) == false) {
		itemLabel.innerHTML = "Must be a number";
		itemLabel.style.color = "red"
	  gotQuanity = false;
	  setSubmitButton();
      return false;
   }
	if (jQuery.isNumeric(quanity) == false) {	
		itemLabel.innerHTML = "Must be a number";
		itemLabel.style.color = "red"
		gotQuanity = false;
	  	setSubmitButton();
      	return false;
	}
	if (min =="bulk"){	
		if (quanity < 10) {	
			itemLabel.innerHTML = "must be 10 or more";
			itemLabel.style.color = "red"
			gotQuanity = false;
			setSubmitButton();
			return false;
		}
		if (quanity > 51) {	
			itemLabel.innerHTML = "must be 50 or less";
			itemLabel.style.color = "red"
			gotQuanity = false;
			setSubmitButton();
			item.value = "please call 412-530-5131 to order";
			return false;
		}
	}
	itemLabel.innerHTML = "How many books";
	itemLabel.style.color = "black"
	gotQuanity = true;
	setSubmitButton();
	return true;
}
	
function itemCheck(itemname, label, control){
	if (document.getElementById(itemname).value.length < 1) {
		document.getElementById(label).style.color="red";
		setControl(itemname, false);
		setSubmitButton();
		return false;
	}
	else {
		control = true;
		document.getElementById(label).style.color="black";
		setControl(itemname, true);
		setSubmitButton();
		return true;
	}		
}
function itemCheck2(itemname, label, control){
	if (document.getElementById(itemname).value.length < 1) {
		document.getElementById(label).style.color="red";
		setControl(itemname, true);
		setSubmitButton2();
		return false;
	}
	else {
		control = true;
		document.getElementById(label).style.color="black";
		setControl(itemname, true);
		setSubmitButton();
		return true;
	}		
}
	function setControl(itemName, newValue) {
		switch (itemName) {
			case "addr1":
			case "addr2":
			case "citystate":
			case "name":
			case "store":
				gotAddr = newValue;
				break;
			default:
				alert ("setControl:Invalid name of " + itemName)
				break;
		}
		return true;
	}

function setSubmitButton()
{
	item = document.getElementById("orderit");
	if (gotEmail && gotAddr && gotQuanity){
		item.value = "Send e-mail to Shipping Clerk.";
		item.disabled = false;
	} else {
		item.value = "Enter valid information above." ;
		item.disabled = true;
	}
}
