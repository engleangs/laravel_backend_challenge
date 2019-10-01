<!DOCTYPE html>
<html>
<head>
	<title>Ordering transaction</title>
</head>
<body>
   
<center>
<h2 style="padding: 23px;background: #b3deb8a1;border-bottom: 6px green solid;">
	<a href="https://itsolutionstuff.com">Below is your recent transacton</a>
</h2>
</center>
  
<p>Hi, <b> {{$data['customer']->name}} </b></p>
<p> Thank for your ordering . Below is your summary of the transaction <b> #{{$data['order']->order_id}} </b> </p>
<h3 style="border-bottom:1px solid black;width:50%" > Purchased Products  </h3>
<table style="border-collapse: collapse;width:50%">
    <tr>
        <th style=" border: 1px solid black"> Product </th>
        <th style=" border: 1px solid black"> Quantity</th>
        <th style=" border: 1px solid black"> Unit Cost</th>
        <th style=" border: 1px solid black"> Amount </th>
    </tr>
@foreach ($data['order_details'] as $item)
    <tr style=" border: 1px solid black">
            <td style=" border: 1px solid black">
                <strong>{{$item->product_name}} </strong> [ <i>{{$item->attributes}} </i> ]
            </td>
            <td style=" border: 1px solid black">
                {{$item->quantity}}
            </td>
            <td style=" border: 1px solid black">
                {{$item->unit_cost}}
            </td>
            <td style=" border: 1px solid black">
                $ {{ $item->quantity*$item->unit_cost}}
            </td>

        </tr>
@endforeach
    
    <tr style=" border: 1px solid black">
        <td style=" border: 1px solid black" colspan="3">
          <b>  Total Amount </b>
        </td>
        <td style=" border: 1px solid black">
            $ {{$data['order']->total_amount}} 
        </td>
    </tr>
</table>
<h3 style="border-bottom:1px solid black;width:50%"  >Shipping Address </h3>
<table cellpadding="10" style="border-collapse: collapse;width:50% " >
    <tr style="border-bottom: 1px solid black">
        <td style=" border-bottom: 1px solid black" align="right"> Type </td>
        <td style=" border-bottom: 1px solid black" align="right"> : </td>
        <td style=" border-bottom: 1px solid black"> {{$data['shipping']->shipping_type}} </td>
    </tr>
    <tr style=" border-bottom: 1px solid black">
        <td style=" border-bottom: 1px solid black" align="right"> Cost </td>
        <td style=" border-bottom: 1px solid black" align="right"> : </td>
        <td style=" border-bottom: 1px solid black"> $ {{$data['shipping']->shipping_cost}}

    </tr>
    <tr style="border-bottom: 1px solid black">
        <td style=" border-bottom: 1px solid black" align="right"> Region </td>
        <td style=" border-bottom: 1px solid black" align="right"> : </td>
        <td style=" border-bottom: 1px solid black"> {{$data['shipping']->shipping_region}} </td>
    </tr>
</table>
  
  
</body>
</html>