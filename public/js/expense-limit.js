document.getElementById("oldExpenseCategoryName").onchange = expenseCategoryLimit;



function expenseCategoryLimit()
{
    let expense_category = document.getElementById("oldExpenseCategoryName").value;    
    document.getElementById("editedExpenseCategory").value = expense_category; 
    document.getElementById("editedExpenseCategory").innerText = expense_category;

       
    const getExpenseLimit = () => {
        fetch(`/Settings/getExpenseCategoryLimitForSettings/?expense_category=${expense_category}`) 
        .then((response) => response.json())        
        .then(response => document.getElementById("limitAmount").innerText = (response))
        .then(response => document.getElementById("limitAmount").value = (response))           
    }

    getExpenseLimit();  

};

 








