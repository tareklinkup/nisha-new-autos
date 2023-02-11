<div class="row">
<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
	<div class="form-group" style="margin-top:10px;">
		<label class="col-sm-1 col-sm-offset-2 control-label no-padding-right" for="salesInvoiceno"> Invoice no </label>
		<label class="col-sm-1 control-label no-padding-right" for="salesInvoiceno"> : </label>
		<div class="col-sm-3">
			<select id="purchasemsid" data-placeholder="Choose a Invoice..." class="chosen-select" style="">
				<option value=""></option>
				<?php 
					if(isset($invoices) && $invoices){
				foreach($invoices as $invoice){ ?>
				<option value="<?php echo $invoice->PurchaseMaster_SlNo; ?>"><?php echo $invoice->PurchaseMaster_InvoiceNo; ?></option>
				<?php } } ?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-2">
			<input type="button" class="btn btn-primary" onclick="searchpurchase()" value="Show Report" style="margin-top:0px;width:150px;">
		</div>
	</div>
</div>
</div>

<div id="PurchaseBill"></div>
<script type="text/javascript">
  function searchpurchase(){
    var purchasemsid = $("#purchasemsid").val();
    if(purchasemsid==""){
      $("#purchasemsid").css('border-color','red');
      return false;
    }else{
        $("#purchasemsid").css('border-color','green');
    }
    var inputData = 'purchasemsid='+purchasemsid;
    var urldata = "<?php echo base_url();?>purchaseInvoiceSearch";
    $.ajax({
        type: "POST",
        url: urldata,
        data: inputData,
        success:function(data){
            $("#PurchaseBill").html(data);
        }
    });
  }
</script>
