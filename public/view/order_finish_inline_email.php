<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <!-- <title>A simple, clean, and responsive HTML invoice template</title> -->
    
    <style>
    
@media only screen and (max-width: 600px) {
  .invoice-box table tr.top table td {
    width: 100%;
    display: block;
    text-align: center;
  }

  .invoice-box table tr.information table td {
    width: 100%;
    display: block;
    text-align: center;
  }
}
</style>
</head>

<body>
    <div class="invoice-box" style="max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; line-height: 24px; font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; color: #555;">
        <table cellpadding="0" cellspacing="0" style="width: 100%; line-height: inherit; text-align: left;" width="100%" align="left">
            <tr class="top">
                <td colspan="2" style="padding: 5px; vertical-align: top;" valign="top">
                    <table style="width: 100%; line-height: inherit; text-align: left;" width="100%" align="left">
                        <tr>
                            <td class="title" style="padding: 5px; vertical-align: top; padding-bottom: 20px; font-size: 45px; line-height: 45px; color: #333;" valign="top">
                                <img src="http://vtal.id/image/front/vtallogo.png" style="width:100%; max-width:100px;">
                                <span style="vertical-align: text-top;">VTal</span>
                            </td>
                            
                            <td style="padding: 5px; vertical-align: top; text-align: right; padding-bottom: 20px;" valign="top" align="right">
                                Invoice #: 123<br>
                                Created: January 1, 2015<br>
                                Due: February 1, 2015
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2" style="padding: 5px; vertical-align: top;" valign="top">
                    <table style="width: 100%; line-height: inherit; text-align: left;" width="100%" align="left">
                        <tr>
                            <td style="padding: 5px; vertical-align: top; padding-bottom: 40px;" valign="top">
                                Sparksuite, Inc.<br>
                                12345 Sunny Road<br>
                                Sunnyville, CA 12345
                            </td>
                            
                            <td style="padding: 5px; vertical-align: top; text-align: right; padding-bottom: 40px;" valign="top" align="right">
                                Acme Corp.<br>
                                John Doe<br>
                                john@example.com
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td style="padding: 5px; vertical-align: top; background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;" valign="top">
                    Payment Method
                </td>
                
                <td style="padding: 5px; vertical-align: top; text-align: right; background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;" valign="top" align="right">
                    Check #
                </td>
            </tr>
            
            <tr class="details">
                <td style="padding: 5px; vertical-align: top; padding-bottom: 20px;" valign="top">
                    Check
                </td>
                
                <td style="padding: 5px; vertical-align: top; text-align: right; padding-bottom: 20px;" valign="top" align="right">
                    1000
                </td>
            </tr>
            
            <tr class="heading">
                <td style="padding: 5px; vertical-align: top; background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;" valign="top">
                    Item
                </td>
                
                <td style="padding: 5px; vertical-align: top; text-align: right; background: #eee; border-bottom: 1px solid #ddd; font-weight: bold;" valign="top" align="right">
                    Price
                </td>
            </tr>
            
            <tr class="item">
                <td style="padding: 5px; vertical-align: top; border-bottom: 1px solid #eee;" valign="top">
                    Website design
                </td>
                
                <td style="padding: 5px; vertical-align: top; text-align: right; border-bottom: 1px solid #eee;" valign="top" align="right">
                    $300.00
                </td>
            </tr>
            
            <tr class="item">
                <td style="padding: 5px; vertical-align: top; border-bottom: 1px solid #eee;" valign="top">
                    Hosting (3 months)
                </td>
                
                <td style="padding: 5px; vertical-align: top; text-align: right; border-bottom: 1px solid #eee;" valign="top" align="right">
                    $75.00
                </td>
            </tr>
            
            <tr class="item last">
                <td style="padding: 5px; vertical-align: top; border-bottom: none;" valign="top">
                    Domain name (1 year)
                </td>
                
                <td style="padding: 5px; vertical-align: top; text-align: right; border-bottom: none;" valign="top" align="right">
                    $10.00
                </td>
            </tr>
            
            <tr class="total">
                <td style="padding: 5px; vertical-align: top;" valign="top"></td>
                
                <td style="padding: 5px; vertical-align: top; text-align: right; border-top: 2px solid #eee; font-weight: bold;" valign="top" align="right">
                   Total: $385.00
                </td>
            </tr>
        </table>
    </div>

    
</body>
</html>