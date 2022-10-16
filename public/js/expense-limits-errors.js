document.getElementById("expense_category").onchange = expenseLimit;




function expenseLimit()
{

    let expense_category = document.getElementById("expense_category").value;    
    let expense_amount = document.getElementById("expense_amount").value;    
    let expense_date = document.getElementById("expense_date").value;
    if (expense_amount === "") 
    {
  		expense_amount = 0;
	}
    
    const getExpenseLimit = () => {
    	fetch(`/addExpense/getExpenseCategoryLimit/?expense_category=${expense_category}&expense_date=${expense_date}&expense_amount=${expense_amount}`) 
        .then((response) => response.json())        
        .then(response => document.getElementById("expenses_limit").innerText = (response))           
    }

    getExpenseLimit();
    document.getElementById('switcher').selected = 'selected';
    document.getElementById('switcher').value = expense_category;
    document.getElementById('switcher').innerText = expense_category;

};

