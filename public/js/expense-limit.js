document.getElementById("incomeCategory").onchange = incomeCategoryLimit;

let income_category = document.getElementById("incomeCategory").value;   
function incomeCategoryLimit()
{
    document.getElementById("editedIncomeCategory").value = income_category; 
    document.getElementById("editedIncomeCategory").innerText = income_category; 
};

 







$(document).ready(function(){


    
    $(".btnEdit, .btnDelete").click(function()
        {
            var me = $(this);
            var result = me.val().split('|');
            setInputValues(result);
            checkLimitBox(result);
        });
    $('.modal').on('hidden.bs.modal', function () {
        $('form').find("input[name=categoryName]").each(function(ev)
            {        
                $(this).removeAttr("value");
            });
            $("input:radio").prop("checked", false);
            $('.showCategories, .hideCategories').removeClass('focus active');
            $('.selectCategories').css('display', 'none'); 
    });
});

function setInputValues(result){
    
    $('form').find("input[name=categoryName], input[type=hidden]").each(function(ev)
    {        
        $(this).attr("value", result[0]);
    });
    $('.deleteCategory').text(result[0]);
};

function checkLimitBox(result)
{    

    if (result[1] > 0) {
        $('#limitCheckbox').prop("checked", true);
        $('#limitAmount').css("display", "inline");
        $('#limitAmount').prop("value", result[1]);
    }
    else {
        $('#limitCheckbox').prop("checked", false);
        $('#limitAmount').css("display", "none");
        $('#limitAmount').removeAttr("value");
    }    
}
