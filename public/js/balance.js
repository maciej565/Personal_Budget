$(document).ready(function() 
{
    let income = JSON.parse(incomeEncoded);
    let expense = JSON.parse(expenseEncoded);

    let incomeTable = getTable(income);
    let expenseTable = getExpenseTable(expense);

    showIncomeChart(incomeTable, document.getElementById('income_chart'));
    showExpenseChart(expenseTable, document.getElementById('expense_chart'));

    

    $(window).resize(function() 
    {
        showIncomeChart(incomeTable, document.getElementById('income_chart'));
        showExpenseChart(expenseTable, document.getElementById('expense_chart'));
    });
});



let comparator = function(a, b) 
{
    if (a[1] < b[1]) return 1;
    if (a[1] > b[1]) return -1;
    return 0;
}

let getTable = function(arrayToFetchData) 
{
    let table = [['Kategoria', 'Kwota']];

    arrayToFetchData.forEach(element => 
    {
        let isReapeted = false;
        for (let i = 0; i < table.length; i++) 
        {
            if(table.length == 1) {
                table.push([element['name'], 0]);
                isReapeted = true;
                break;
            }
            else if(element['name'] == table[i][0])
             {
                isReapeted = true;
                break;
            }
        }
        if(!isReapeted) 
        {
            table.push([element['name'], 0]);
            isReapeted = false;
        }
    });

    table.forEach(element => 
    {
        for (let i = 0; i < arrayToFetchData.length; i++) 
        {
            if (element[0] == arrayToFetchData[i]['name']) 
            {
                element[1] = parseFloat(element[1]) + parseFloat(arrayToFetchData[i]['amount']);
            }
        }
    });

    table.sort(comparator);

    return table;
};

let getExpenseTable = function(arrayToFetchData) 
{
    let table = [['Kategoria', 'Kwota']];

    arrayToFetchData.forEach(element => 
    {
        let isReapeted = false;
        for (let i = 0; i < table.length; i++) 
        {
            if(table.length == 1) {
                table.push([element['expense_name'], 0]);
                isReapeted = true;
                break;
            }
            else if(element['expense_name'] == table[i][0])
             {
                isReapeted = true;
                break;
            }
        }
        if(!isReapeted) 
        {
            table.push([element['expense_name'], 0]);
            isReapeted = false;
        }
    });

    table.forEach(element => 
    {
        for (let i = 0; i < arrayToFetchData.length; i++) 
        {
            if (element[0] == arrayToFetchData[i]['expense_name']) 
            {
                element[1] = parseFloat(element[1]) + parseFloat(arrayToFetchData[i]['amount']);
            }
        }
    });

    table.sort(comparator);

    return table;
};


let showIncomeChart = function(table, element) 
{
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    // Draw the chart and set the chart values
    function drawChart() 
    {
    let data = google.visualization.arrayToDataTable(table);

    // Optional; add a title and set the width and height of the chart
    let Options = 
    {
       title:'',
        width:'100%',
        height:400,
        is3D:true,              
        chartArea: 
        {           
            width: '90%',
            height: 'auto',

        },       
        titleTextStyle: 
        {
            alignment: 'center',
            fontName: "'Oswald', sans-serif",        
            fontSize: 24,
            color: '#000000'
        },
        legend: 
        {
            position: 'bottom', 
            alignment: 'center',
            textStyle: 
            {
                fontName: "'Oswald', sans-serif",   
                fontSize: 16,
                color: '#000000'
            }
        }
    };

    let chart = new google.visualization.PieChart(element);
    chart.draw(data, Options);   
    }
}

let showExpenseChart = function(table, element) 
{
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    // Draw the chart and set the chart values
    function drawChart() 
    {
    let data = google.visualization.arrayToDataTable(table);

    // Optional; add a title and set the width and height of the chart
    let Options = 
    {
       title:'',
        width:'100%',
        height:400,
        is3D:true,              
        chartArea: 
        {           
            width: '90%',
            height: 'auto',

        },       
        titleTextStyle: 
        {
            alignment: 'center',
            fontName: "'Oswald', sans-serif",        
            fontSize: 24,
            color: '#000000'
        },
        legend: 
        {
            position: 'bottom', 
            alignment: 'center',
            textStyle: 
            {
                fontName: "'Oswald', sans-serif",   
                fontSize: 16,
                color: '#000000'
            }
        }
    };

    let chart = new google.visualization.PieChart(element);
    chart.draw(data, Options);   
    }
}





