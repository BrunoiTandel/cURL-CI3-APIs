var options = {
  "key": 'API Key', // Enter the Key ID generated from the Dashboard    
  "amount": 'Amount in integer / float' * 100, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise    
  "currency": "INR",    
  "name": "Name of the Company",
  "description": "Description of the purchase",
  "image":'',
  "handler": function (response) {
    store_order(response);
  },
  "prefill": {
    "name": 'Customer Name',
    "email": 'Customer Email ID',
    "contact": 'Customer Contact number'
  },
  "theme": {
    "color": "transparent linear-gradient(283deg, #141414 0%, #141414 100%)"
  }
};
var rzp1 = new Razorpay(options);
rzp1.open();
e.preventDefault();