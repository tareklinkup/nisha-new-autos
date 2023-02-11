<style>
	.v-select{
		margin-bottom: 5px;
		float:right;
		min-width: 200px;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
		height: 25px;
	}
	.v-select input[type=search], .v-select input[type=search]:focus{
		margin: 0px;
	}
	.v-select .vs__selected-options{
		overflow: hidden;
		flex-wrap:nowrap;
	}
	.v-select .selected-tag{
		margin: 2px 0px;
		white-space: nowrap;
		position:absolute;
		left: 0px;
	}
	.v-select .vs__actions{
		margin-top:-5px;
	}
	.v-select .dropdown-menu{
		width: auto;
		overflow-y:auto;
	}
	#table1{
		border-collapse: collapse;
		width: 100%;
	}

	#table1 td, #table1 th{
		padding: 5px;
		border: 1px solid #909090;
	}
	select.form-control {
    padding: 1px 6px;
}

	#table1 th{
		text-align: center;
	}

	#table1 thead{
		background-color: #cbd6e7;
	}
</style>
<div id="profitLoss">
	<div class="row" style="border-bottom: 1px solid #ccc;">
		<div class="col-md-12">
			<form class="form-inline" v-on:submit.prevent="getProfitLoss" >
			<div class="form-group">
					<label>Search Type</label>
					<select class="form-control" v-model="searchType" @change="onChangeSearchType">
						<option value="">All</option>
						<option value="customer">By Customer</option>
						<option value="category">By Group</option>
						<option value="quantity">By Product</option>
					</select>
				</div>
				<div class="form-group"  style="display:none;" v-bind:style="{display: searchType == 'customer' && customers.length > 0 ? '' : 'none'}">
					<label>Customer &nbsp;</label>
					<v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name" placeholder="Select Customer"></v-select>
				</div>
				
				<div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'quantity' && products.length > 0 ? '' : 'none'}">
					<label>Product</label>
					<v-select v-bind:options="products" v-model="selectedProduct" label="display_text"></v-select>
				</div>

				<div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'category' && brand.length > 0 ? '' : 'none'}">
					<label> Group </label>
					<v-select v-bind:options="brand" v-model="selectedBrand" label="brand_name"></v-select>
				</div>

				<div class="form-group">
					<label>Date from </label>
					<input type="date" class="form-control" v-model="filter.dateFrom">
				</div>

				<div class="form-group">
					<label>to </label>
					<input type="date" class="form-control" v-model="filter.dateTo">
				</div>

				<div class="form-group">
					<input type="submit" class="btn btn-info btn-xs" value="Search" style="padding-top:0px;padding-bottom:0px;margin-top:-4px;">
				</div>
			</form>
		</div>
	</div>

	<div class="row" style="padding: 10px 0;display:none;" v-bind:style="{display: reportData.length > 0 ? '' : 'none'}">
		<div class="col-md-12">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
	</div>

	<div class="row" style="margin-top: 10px;">
		<div class="col-md-12">
			<div class="table-responsive" id="reportTable">
				<table id="table1"  style="display:none;" v-bind:style="{display: reportData.length > 0 ? '' : 'none'}">
					<thead>
						<tr>
							<th>Product Id</th>
							<th>Product</th>
							<th>Sold Quantity</th>
							<th>Purchase Rate</th>
							<th>Purchased Total</th>
							<th>Sold Amount</th>
							<th>Profit/Loss</th>
						</tr>
					</thead>
					<tbody v-for="data in reportData">
						<tr>
							<td colspan="7" style="background-color: #e3eae7;">
								<strong>Invoice: </strong> {{ data.SaleMaster_InvoiceNo }} | 
								<strong>Sales Date: </strong> {{ data.SaleMaster_SaleDate }} | 
								<strong>Customer: </strong> {{ data.Customer_Name }} |
								<strong>Discount: </strong> {{ data.SaleMaster_TotalDiscountAmount | decimal }} |
								<strong>VAT: </strong> {{ data.SaleMaster_TaxAmount | decimal }} |
								<strong>Transport Cost: </strong> {{ data.SaleMaster_Freight | decimal }}
							</td>
						</tr>
						<tr v-for="product in data.saleDetails">
							<td>{{ product.Product_Code }}</td>
							<td>{{ product.Product_Name }}</td>
							<td style="text-align:right;">{{ product.SaleDetails_TotalQuantity }}</td>
							<td style="text-align:right;">{{ product.averagePurchasePrice }}</td>
							<td style="text-align:right;">{{ product.purchased_amount }}</td>
							<td style="text-align:right;">{{ product.SaleDetails_TotalAmount }}</td>
							<td style="text-align:right;">{{ product.profit_loss }}</td>
						</tr>
						<tr style="background-color: #f0f0f0;font-weight: bold;">
							<td colspan="4" style="text-align:right;">Total</td>
							<td style="text-align:right;">{{ data.saleDetails.reduce((prev, cur) => { return prev + parseFloat(cur.purchased_amount) }, 0) }}</td>
							<td style="text-align:right;">{{ data.saleDetails.reduce((prev, cur) => { return prev + parseFloat(cur.SaleDetails_TotalAmount) }, 0) }}</td>
							<td style="text-align:right;">{{ data.saleDetails.reduce((prev, cur) => { return prev + parseFloat(cur.profit_loss) }, 0) }}</td>
						</tr>
					</tbody>
					<tfoot style="display:none;font-weight:bold;background-color:#e9dcdc;" v-bind:style="{display: reportData.length > 0 ? '' : 'none'}">
						<tr>
							<td style="text-align:right;" colspan="4">Total Profit</td>
							<td style="text-align:right;">
								{{ 
									reportData.reduce((prev, cur) => { return prev + parseFloat(
										cur.saleDetails.reduce((p, c) => { return p + parseFloat(c.purchased_amount) }, 0)
									)}, 0).toFixed(2)
								}}
							</td>
							<td style="text-align:right;">
								{{ 
									reportData.reduce((prev, cur) => { return prev + parseFloat(
										cur.saleDetails.reduce((p, c) => { return p + parseFloat(c.SaleDetails_TotalAmount) }, 0)
									)}, 0).toFixed(2)
								}}
							</td>
							<td style="text-align:right;">
								{{ 
									totalProfit = reportData.reduce((prev, cur) => { return prev + parseFloat(
										cur.saleDetails.reduce((p, c) => { return p + parseFloat(c.profit_loss) }, 0)
									)}, 0).toFixed(2)
								}}
							</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Other Income (+)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ otherIncome | decimal }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">VAT (+)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ totalVat = reportData.reduce((prev, cur) => { return prev + parseFloat(cur.SaleMaster_TaxAmount) }, 0).toFixed(2) }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Total Discount (-)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ totalDiscount = reportData.reduce((prev, cur) => { return prev + parseFloat(cur.SaleMaster_TotalDiscountAmount) }, 0).toFixed(2) }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Total Returned Value (-)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.returned_amount | decimal }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Total Damaged (-)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.damaged_amount | decimal }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Cash Transaction (-)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.expense | decimal }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Employee Payment (-)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ ( parseFloat(otherIncomeExpense.employee_payment) + parseFloat(otherIncomeExpense.employee_payment_old))  | decimal }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Profit Distribute (-)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.profit_distribute | decimal }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Loan Interest (-)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.loan_interest | decimal }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Vat Payment (-)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.vat_payment | decimal }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Assets Sales | Profit/Loss (-)</td>
							<td colspan="2"></td>
							<td style="text-align:right;">{{ otherIncomeExpense.assets_sales_profit_loss | decimal }}</td>
						</tr>

						<tr>
							<td colspan="4" style="text-align:right;">Profit</td>
							<td colspan="2"></td>
							<td style="text-align:right;">
								{{  (
										(
											parseFloat(totalProfit) + 
											parseFloat(totalVat) + 
											parseFloat(otherIncome)
										) - 
										(
											parseFloat(totalDiscount) + 
											parseFloat(otherIncomeExpense.returned_amount) + 
											parseFloat(otherIncomeExpense.damaged_amount) + 
											parseFloat(otherIncomeExpense.expense) + 
											parseFloat(otherIncomeExpense.employee_payment) + 
											parseFloat(otherIncomeExpense.employee_payment_old) + 
											parseFloat(otherIncomeExpense.profit_distribute) + 
											parseFloat(otherIncomeExpense.loan_interest) + 
											parseFloat(otherIncomeExpense.assets_sales_profit_loss) +
											parseFloat(otherIncomeExpense.vat_payment)
										)
									).toFixed(2) }}
							</td>
						</tr>
					</tfoot>
				</table>

				<table id="table1" style="display:none;" v-bind:style="{display: productProfitlLoss.length > 0 ? '' : 'none'}">
					<thead>
						<tr>
							<th>Product Id</th>
							<th>Product</th>
							<th>Group</th>
							<th>Sold Quantity</th>
							<th>Purchase Rate</th>
							<th>Purchased Total</th>
							<th>Sold Amount</th>
							<th>Profit/Loss</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="product in productProfitlLoss">
							<td>{{ product.Product_Code }}</td>
							<td>{{ product.Product_Name }}</td>
							<td>{{ product.brand_name }}</td>
							<td style="text-align:right;">{{ product.SaleDetails_TotalQuantity }}</td>
							<td style="text-align:right;">{{ product.averagePurchasePrice }}</td>
							<td style="text-align:right;">{{ product.purchased_amount }}</td>
							<td style="text-align:right;">{{ product.SaleDetails_TotalAmount }}</td>
							<td style="text-align:right;">{{ product.profit_loss }}</td>
						</tr>
				
					</tbody>
					<tfoot style="display:none;" v-bind:style="{display: productProfitlLoss.length > 0 ? '' : 'none'}">
						<tr>
							<td style="text-align:right;" colspan="3">Total</td>
							<td style="text-align:right;">
								{{ 
									productProfitlLoss.reduce((prev, cur) => { return prev + parseFloat(cur.SaleDetails_TotalQuantity)}, 0).toFixed(2)
								}}
							</td>
							<td style="text-align:right;">
								{{ 
									productProfitlLoss.reduce((prev, cur) => { return prev + parseFloat(cur.averagePurchasePrice)}, 0).toFixed(2)
								}}
							</td>
							<td style="text-align:right;">
								{{ 
									productProfitlLoss.reduce((prev, cur) => { return prev + parseFloat(cur.purchased_amount)}, 0).toFixed(2)
								}}
							</td>
							<td style="text-align:right;">
								{{ 
									productProfitlLoss.reduce((prev, cur) => { return prev + parseFloat(cur.SaleDetails_TotalAmount)}, 0).toFixed(2)
								}}
							</td>
							<td style="text-align:right;">
								{{ 
									productProfitlLoss.reduce((prev, cur) => { return prev + parseFloat(cur.profit_loss)}, 0).toFixed(2)
								}}
							</td>
					
						</tr>
					</tfoot>
				</table>

			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#profitLoss',
		data(){
			return {
				searchType: '',
				filter: {
					customer: null,
					dateFrom: moment().format('YYYY-MM-DD'),
					dateTo: moment().format('YYYY-MM-DD')
				},
				products: [],
				selectedProduct: null,
				customers: [],
				selectedCustomer: null,
				reportData: [],
				productProfitlLoss:[],
				brand: [],
				selectedBrand: null,
				otherIncomeExpense: {
					income: 0,
					expense: 0,
					employee_payment: 0,
					employee_payment_old: 0,
					profit_distribute: 0,
					loan_interest: 0,
					vat_payment: 0,
					assets_sales_profit_loss: 0,
					damaged_amount: 0,
					returned_amount: 0
				}
				
			}
		},
		filters: {
			decimal(value) {
				return value ==  null || value == undefined ? '0.00' : parseFloat(value).toFixed(2);
			}
		},
		computed:{
			totalTransportCost(){
				return this.reportData.reduce((prev, cur) => { return prev + parseFloat(cur.SaleMaster_Freight) }, 0).toFixed(2);
			},
			otherIncome(){
				return (parseFloat(this.totalTransportCost) + parseFloat(this.otherIncomeExpense.income)).toFixed(2);
			}
		},
		methods: {
			onChangeSearchType(){
				this.sales = [];

				this.selectedCustomer = null;
				this.selectedProduct = null;
				this.selectedBrand = null;
			
			 	if(this.searchType == 'category'){
					this.getbrand();
					this.selectedProduct = null;
					this.selectedCustomer = null;
				}
				else if(this.searchType == 'quantity'){
					this.getProducts();
					this.selectedBrand = null;
					this.selectedCustomer = null;
				} 
				else if(this.searchType == 'customer'){
					this.getCustomers();
					this.selectedBrand = null;
				}
			},
		
			getCustomers(){
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},
			getProducts(){
				axios.get('/get_products').then(res => {
					this.products = res.data;
				})
			},
			getbrand(){
				axios.get('/get_brands').then(res => {
					
					this.brand = res.data;
				})
			},
		

			async getProfitLoss(){
				this.reportData = [];
				this.productProfitlLoss = [];
				
				if(this.searchType == 'customer' || this.searchType == ''){
					this.filter.customer = this.selectedCustomer == null ? null : this.selectedCustomer.Customer_SlNo;
					this.reportData = await axios.post('/get_profit_loss', this.filter).then(res => {
						return res.data;
					})
				} else {
					this.filter.customer = null;
				}
			
				if(this.searchType == 'quantity' || this.searchType == 'category'){
					this.filter.products = this.selectedProduct == null ? null : this.selectedProduct.Product_SlNo;
					this.filter.brand = this.selectedBrand == null ? null : this.selectedBrand.brand_SiNo;
					this.productProfitlLoss = await axios.post('/get_product_profit', this.filter).then(res => {
						return res.data;
					})
				} else {
					this.filter.products = null;
					this.filter.brand = null;
				}
				
				this.otherIncomeExpense = await axios.post('/get_other_income_expense', this.filter).then(res => {
					return res.data;
				})
				
			},

			async print(){
				let customerText = '';
				if(this.selectedCustomer != null){
					customerText = `
						<strong>Customer Id: </strong> ${this.selectedCustomer.Customer_Code}<br>
						<strong>Name: </strong> ${this.selectedCustomer.Customer_Name}<br>
						<strong>Address: </strong> ${this.selectedCustomer.Customer_Address}<br>
						<strong>Mobile: </strong> ${this.selectedCustomer.Customer_Mobile}
					`;
				}

				let dateText = '';
				if(this.filter.dateFrom != '' && this.filter.dateTo != ''){
					dateText = `
						Statement from <strong>${this.filter.dateFrom}</strong> to <strong>${this.filter.dateTo}</strong>
					`;
				}
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Profit/Loss Report</h4 style="text-align:center">
						<div class="row">
							<div class="col-md-6">${customerText}</div>
							<div class="col-md-6 text-right">${dateText}</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

				var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php');?>
				`);

				mywindow.document.head.innerHTML += `
					<style>
						#table1{
							border-collapse: collapse;
							width: 100%;
						}

						#table1 td, #table1 th{
							padding: 5px;
							border: 1px solid #909090;
						}

						#table1 th{
							text-align: center;
						}

						#table1 thead{
							background-color: #cbd6e7;
						}
					</style>
				`;
				mywindow.document.body.innerHTML += reportContent;

				mywindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				mywindow.print();
				mywindow.close();
			}
		}
	})
</script>