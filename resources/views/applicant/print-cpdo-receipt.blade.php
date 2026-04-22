<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CPDO Fee Assessment Receipt</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            line-height: 1.5; 
            margin: 0; 
            padding: 20px; 
            background-color: #f5f5f5;
        }
        .receipt-container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white;
            border: 2px solid #155386; 
            border-radius: 8px; 
            padding: 30px;
        }
        .receipt-header { 
            text-align: center; 
            border-bottom: 2px solid #155386; 
            padding-bottom: 15px; 
            margin-bottom: 20px;
        }
        .receipt-header h2 { margin: 0; color: #155386; font-size: 20px; }
        .receipt-header p { margin: 5px 0; font-size: 12px; color: #666; }
        .receipt-title { text-align: center; margin: 15px 0; }
        .receipt-title h3 { margin: 0; text-transform: uppercase; letter-spacing: 2px; font-size: 14px; }
        .receipt-details { margin: 20px 0; }
        .receipt-details table { width: 100%; border-collapse: collapse; }
        .receipt-details td { padding: 8px; border: none; }
        .receipt-details td:first-child { font-weight: 600; width: 40%; }
        .receipt-items { margin: 20px 0; }
        .receipt-items table { width: 100%; border-collapse: collapse; }
        .receipt-items th, .receipt-items td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
        .receipt-items th { background: #f8f9fa; font-weight: 600; }
        .receipt-items td:last-child { text-align: right; }
        .receipt-total { margin: 20px 0; text-align: right; }
        .receipt-total table { width: 100%; }
        .receipt-total td { padding: 8px; }
        .receipt-total .total-label { font-weight: bold; font-size: 16px; }
        .receipt-total .total-amount { font-weight: bold; font-size: 18px; color: #155386; }
        .signature-section { margin-top: 40px; border-top: 1px solid #dee2e6; padding-top: 30px; }
        .signature-line { margin-top: 30px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 30px; }
        .signature-box { text-align: center; flex: 1; min-width: 200px; }
        .signature-box .line { border-top: 1px solid #333; width: 100%; margin: 30px 0 5px; }
        .signature-box .name { font-weight: bold; margin-top: 10px; }
        .signature-box .title { font-size: 12px; color: #666; }
        .signature-box .wet-signature-note { font-size: 10px; color: #dc2626; font-style: italic; margin-top: 5px; }
        
        .no-print { text-align: center; margin-top: 20px; }
        .no-print button { 
            padding: 10px 20px; 
            background: #155386; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            margin: 0 5px;
            font-size: 14px;
        }
        .no-print button.close-btn { background: #6c757d; }
        
        @media print {
            body { background: white; padding: 0; }
            .receipt-container { border: 1px solid #000; padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>CITY GOVERNMENT OF LIGAO</h2>
            <p>CITY PLANNING AND DEVELOPMENT OFFICE</p>
            <p>CPDO Fee Assessment Receipt</p>
        </div>
        
        <div class="receipt-title">
            <h3>OFFICIAL FEE ASSESSMENT</h3>
        </div>
        
        <div class="receipt-details">
            <table>
                <tr><td style="font-weight: 600; width: 40%;">Application Number:</td>
                <td><strong><?php echo e($application->application_number); ?></strong></td>
                </tr>
                <tr><td style="font-weight: 600;">Applicant Name:</td>
                <td><strong><?php echo e($applicantName); ?></strong></td>
                </tr>
                <tr><td style="font-weight: 600;">Assessment Date:</td>
                <td><strong><?php echo e($assessmentData['assessment_date'] ?? date('F d, Y')); ?></strong></td>
                </tr>
            </table>
        </div>
        
        <div class="receipt-items">
            <h4>Fee Breakdown:</h4>
            <table>
                <thead>
                    <tr><th>Description</th><th>Amount</th></tr>
                </thead>
                <tbody>
                    <?php if(($assessmentData['zonal_location_fee'] ?? 0) > 0): ?>
                    <tr><td>Locational Clearance</td><td><?php echo $formatAmount($assessmentData['zonal_location_fee']); ?></td></tr>
                    <?php endif; ?>
                    <?php if(($assessmentData['palc_fee'] ?? 0) > 0): ?>
                    <tr><td>PALC Fee</td><td><?php echo $formatAmount($assessmentData['palc_fee']); ?></td></tr>
                    <?php endif; ?>
                    <?php if(($assessmentData['development_permit_fee'] ?? 0) > 0): ?>
                    <tr><td>Development Permit</td><td><?php echo $formatAmount($assessmentData['development_permit_fee']); ?></td></tr>
                    <?php endif; ?>
                    <?php if(($assessmentData['alteration_permit_fee'] ?? 0) > 0): ?>
                    <tr><td>Alteration Permit</td><td><?php echo $formatAmount($assessmentData['alteration_permit_fee']); ?></td></tr>
                    <?php endif; ?>
                    <?php if(($assessmentData['site_zoning_certificate_fee'] ?? 0) > 0): ?>
                    <tr><td>Site/Zoning Certificate</td><td><?php echo $formatAmount($assessmentData['site_zoning_certificate_fee']); ?></td></tr>
                    <?php endif; ?>
                    <?php if(!empty($assessmentData['cpdo_additional_fees'])): ?>
                        <?php foreach($assessmentData['cpdo_additional_fees'] as $fee): ?>
                            <?php if(($fee['amount'] ?? 0) > 0): ?>
                            <tr><td><?php echo htmlspecialchars($fee['description'] ?? 'Additional Fee'); ?></td><td><?php echo $formatAmount($fee['amount']); ?></td></tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="receipt-total">
            <table>
                <tr><td class="total-label">TOTAL CPDO FEES:</td>
                <td class="total-amount"><?php echo $formatAmount($assessmentData['total_cpdo_amount'] ?? 0); ?></td>
                </tr>
            </table>
        </div>
        
        <?php if($assessmentData['cpdo_assessment_notes']): ?>
        <div style="margin: 15px 0; padding: 10px; background: #fef3c7; border-left: 4px solid #f59e0b;">
            <strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($assessmentData['cpdo_assessment_notes'])); ?>
        </div>
        <?php endif; ?>
        
        <div class="signature-section">
            <div class="signature-line">
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="name">ASSESSED BY:</div>
                    <div class="title">(Signature over Printed Name)</div>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="name">OSCAR D. AQUINO, EnP</div>
                    <div class="title">ACDH I / Acting CPDC</div>
                </div>
            </div>
            <div style="margin-top: 20px; text-align: center; font-size: 11px; color: #666;">
                <p>Assessment Date: <?php echo e($assessmentData['assessment_date'] ?? date('F d, Y')); ?> | Assessed by: <?php echo e($assessmentData['cpdo_assessed_by'] ?? 'CPDO Staff'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="no-print">
        <button onclick="window.print();">🖨️ Print Receipt</button>
        <button onclick="window.close();" class="close-btn">Close</button>
    </div>
    
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>