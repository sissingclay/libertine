declare var Raven: any;

var form_elements = document.querySelectorAll('[data-form]'),
form = document.querySelector('.lb-apply');

function checkoutForm (form) {

    var isValid,
    count = 0;

    [].forEach.call(form, function (val, index) {

        if(typeof val.getAttribute('data-form') === 'string') {
            isValid = checkFormElements(val, form);

            if(typeof isValid === 'boolean' && !isValid) {
                count = count +1;
            }
        }
    });

    if (count === 0) {
        isValid = true;
    }

    if (count > 0) {
        isValid = false;
    }

    return isValid;
}

function checkFormElements (val, form) {

    var input_value     = val.value,
    error_class         = 'lb-error',
    has_error_class     = hasClass(val, error_class),
    form_valid          = true;


    if (val.type !== 'radio') {

        if (!input_value) {

            form_valid = false;

            if(!has_error_class) {
                addClass(val, error_class);
            }
        }

        if (input_value) {

            var conditional_checks = val.getAttribute('data-form');

            if (!conditional_checks) {

                if (has_error_class) {
                    removeClass(val, error_class);
                }
            }

            if (conditional_checks) {

                var checks = conditional_checks.split(','),
                    is_valid_email;

                checks.forEach(function (to_checks) {

                    if (to_checks === 'email') {
                        is_valid_email = checkEmail(input_value);

                        if (!is_valid_email) {

                            form_valid = false;

                            if (!has_error_class) {
                                addClass(val, error_class);
                            }
                        }

                        if (is_valid_email) {

                            if (has_error_class) {
                                removeClass(val, error_class);
                            }
                        }
                    }

                    if (to_checks === 'idNumber') {
                        const is_valid_idNumber = validateID(input_value);

                        if (!is_valid_idNumber) {

                            form_valid = false;

                            if (!has_error_class) {
                                addClass(val, error_class);
                            }
                        }

                        if (is_valid_idNumber) {

                            if (has_error_class) {
                                removeClass(val, error_class);
                            }
                        }
                    }

                    if (to_checks === 'tel') {
                        const is_valid_phone = validateTel(input_value);
  
                        if (!is_valid_phone) {

                            form_valid = false;

                            if (!has_error_class) {
                                addClass(val, error_class);
                            }
                        }

                        if (is_valid_phone) {

                            if (has_error_class) {
                                removeClass(val, error_class);
                            }
                        }
                    }
                });
}
}
}

if (val.type === 'radio') {
    form_valid = checkedRadioBtn(val.name, error_class);
}

return form_valid;
}

function checkedRadioBtn(sGroupName, error_class)
{
    var group           = document.getElementsByName(sGroupName),
    isChecked       = 0;

    [].forEach.call(group, function (val) {

        if(val.checked) {

            isChecked   = 1;

            [].forEach.call(group, function (ele) {
        if(hasClass(ele.parentNode, error_class)) {
            removeClass(ele.parentNode, error_class);
    }
    });

    return true;
    }
    });

    if(isChecked === 0) {

        [].forEach.call(group, function (ele) {
            if(!hasClass(ele.parentNode, error_class)) {
                addClass(ele.parentNode, error_class);
            }
        });

        return false;
    }
}

function isFormValid (form) {

    var is_for_invalid = true;

    [].forEach.call(form, function (val, index) {

    if(typeof val.getAttribute('data-form') === 'string') {

    if(val.type !== 'radio') {
    val.addEventListener('blur', function (ele) {
    is_for_invalid = checkFormElements(val, form);
    });
    }

    if(val.type === 'radio') {
        val.addEventListener('change', function (ele) {
        is_for_invalid = checkFormElements(val, form);
        });
    }

    }
    });

    return is_for_invalid;
}

isFormValid(form);
form.addEventListener('submit', function(evt) {
    evt.preventDefault();
    var is_valid = checkoutForm(form);

    if(is_valid) {
        document.querySelector('.spinner').className += ' spinner__show';

        ajax().post(form.getAttribute('action'), serialize(form)).success(function(data) {

            var affliant = '';

            if (window.location.pathname === '/apply-debt-relief.html') {
                affliant = '#apply'
            }

            if (window.location.pathname === '/get-in-touch.html') {
                affliant = '#touch'
            }

            if (window.location.pathname === '/credit-clearance.html') {
                affliant = '#credit_clearance'
            }
            
            if (window.location.pathname === '/prescribed-debt.html') {
                affliant = '#prescribed_debt'
            }

            if (window.location.pathname === '/credit-analysis.html') {
                affliant = '#credit_analysis'
            }
            window.location.href =  "/thank-you.html" + affliant;
        }).error(function(data) {
            Raven.captureException(data)
            var errorElement = document.querySelector('.error-js')
            errorElement.className = errorElement.className + ' error-js-visible'
            errorElement.innerHTML = 'Error submitting your data. Please refresh the page and try again. If the problem persists please call(<a href="tel:+27219492211">+27 (21) 949 2211</a>) or <a href="mailto:info@libertineconsultants.co.za">email us</a>.';
        });
    } else {
        document.querySelector('.lb-error').scrollIntoView();
    }
});

function hasClass(el, clss) {
    return el.className && new RegExp("(^|\\s)" +
        clss + "(\\s|$)").test(el.className);
}

function addClass(el, clss) {
    el.className += " " + clss;
}

function removeClass(el, clss) {
    var reg = new RegExp('(\\s|^)' + clss + '(\\s|$)');
    el.className = el.className.replace(reg, ' ');
}

function checkEmail(email_address) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email_address);
}

function validateID(idNumber) {
    var ex = /^(((\d{2}((0[13578]|1[02])(0[1-9]|[12]\d|3[01])|(0[13456789]|1[012])(0[1-9]|[12]\d|30)|02(0[1-9]|1\d|2[0-8])))|([02468][048]|[13579][26])0229))(( |-)(\d{4})( |-)(\d{3})|(\d{7}))/
    var theIDnumber = idNumber
    if (ex.test(theIDnumber) == false) {
        return false
    }
    return true
}

function validateTel(number) {
    var ex = /^[(]?(\+[\d]{1,3}\s?)?[\d ()]{7,}$/g
    var thePhoneNumber = number
    if (ex.test(thePhoneNumber) == false) {
        return false
    }
    return true
}

function ajax () {

    var parse = function(req) {
        var result;
        try {
        result = JSON.parse(req.responseText);
    } catch (e) {
result = req.responseText;
}
return [result, req];
};

var xhr = function(type, url, data) {
    var methods = {
        success: function(callback) {
            this.success = callback;
            return this;
    },
error: function(callback) {
    this.error = callback;
    return this;
}
};
var XHR = XMLHttpRequest || ActiveXObject;
var request = new XHR('MSXML2.XMLHTTP.3.0');
request.open(type, url, true);
request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
request.onreadystatechange = function() {
    if (request.readyState === 4) {
        if (request.status === 200) {
            methods.success.apply(methods, parse(request));
        } else {
            methods.error.apply(methods, parse(request));
        }
    }
};
request.send(data);
return methods;
};

return {
    post: function(url, data) {
        return xhr('POST', url, data);
}
};

}

function serialize(form) {
    if (!form || form.nodeName !== "FORM") {
        return;
}
    var i, j, q = [];
    for (i = form.elements.length - 1; i >= 0; i = i - 1) {
        if (form.elements[i].name === "") {
            continue;
        }
        switch (form.elements[i].nodeName) {
            case 'INPUT':
                switch (form.elements[i].type) {
                    case 'text':
                    case 'hidden':
                    case 'password':
                    case 'button':
                    case 'reset':
                    case 'submit':
                        q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        break;
                    case 'checkbox':
                    case 'radio':
                        if (form.elements[i].checked) {
                            q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        }
                        break;
                    case 'file':
                        break;
                    }
                break;
            case 'TEXTAREA':
                q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                break;
            case 'SELECT':
                switch (form.elements[i].type) {
                    case 'select-one':
                        q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                    break;
                    case 'select-multiple':
                        for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
                            if (form.elements[i].options[j].selected) {
                                q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
                            }
                        }
                    break;
                }
                break;
            case 'BUTTON':
                switch (form.elements[i].type) {
                    case 'reset':
                    case 'submit':
                    case 'button':
                        q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                    break;
                }
                break;
        }
    }
    return q.join("&");
}
