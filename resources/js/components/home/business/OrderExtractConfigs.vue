<template>
	<div>
		<div class="row">
			<div class="col-md-8"></div>
			<div class="col-md-4">
				<div class="form-group">
					<b-form-file
						v-model="file"
						:state="Boolean(file)"
						placeholder="Chọn file để bắt đầu cấu hình..."
						drop-placeholder="Drop file here..."
					></b-form-file>
				</div>
			</div>
		</div>

		<div class="row">
			<div
				class="col-8 first-phase-result"
				style="display: flex; flex-direction: column; gap: 1rem"
			>
				<div
					class="card-rate"
					v-for="(review, index) in frist_phase_result"
					:key="index"
					header-tag="header"
				>
					<div class="time">Bảng thứ {{ index + 1 }}</div>
					<div class="line"></div>
					<div class="container-rate">
						<div class="box-rate">
							<div class="review-content">
								{{ review }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<b-card>
					<div class="form-group">
                        <label for="method">Method</label>
                        <small class="text-danger">*</small>
                        <treeselect :multiple="false"
                                    id="method"
                                    placeholder="Chọn cách thức.."
                                    v-model="form_filter.method"
                                    required />
                    </div>
                    <div class="form-group">
                        <label for="camelotFlavor">Camelot Flavor</label>
                        <small class="text-danger">*</small>
                        <treeselect
                                id="camelotFlavor"
                                placeholder="Chọn cấu hình.."
                                v-model="form_filter.camelotFlavor"
                                required
                                />
                    </div>
                    <div class="form-group">
                        <label>Merge Pages</label>
                        <input
                            type="checkbox"
                            id="mergePages"
                            v-model="mergePages"
                            class="form-check-input"
                        />
                        </div>
                        <div class="form-group">
                            <label >Exclude head tables count</label>
                            <input
                                type='number'
                                v-model="excludeHeadTablesCount"
                                class="form-number-input"
                            />
                        </div>

                            <div class="form-group">
                            <label >Exclude tail tables count</label>
                            <input
                                type="number"
                                v-model="excludeTailTablesCount"
                                class="form-number-input"
                            />
                            </div>
				</b-card>
			</div>
		</div>
	</div>
</template>

<script>
	import Treeselect, { ASYNC_SEARCH } from '@riophae/vue-treeselect';
	import '@riophae/vue-treeselect/dist/vue-treeselect.css';
	export default {
		components: {
			Treeselect,
		},
		data() {
			return {



				file: null,
				frist_phase_form: {},
				frist_phase_result: [
					'"Thông tin xuất hóa đơn (Billing Information)\nCÔNG TY CỔ PHẦN DỊCH VỤ THƯƠNG MẠI TỔNG HỢP\nWINCOMMERCE\nTầng 5, Mplaza SaiGon, số 39 Lê Duẩn, Phường Bến Nghé, Quận 1,\nThành phố Hồ Chí Minh, Việt Nam\nMST: 0104918404\nĐịa chỉ giao hàng (Delivery Address)\n1511-WM VCP HCM Ba Tháng Hai\n3-3C Ba Tháng Hai, P. 11, Quận 10, TP. Hồ Chí Minh Việt Nam\n02471066866 -...","Thông tin đơn hàng (Information)\nSố đơn hàng (PO No.)\n4147499541\nNgày đặt hàng (PO date)\n08.03.2023\nNhóm đặt hàng (Purchaser)\n203 - Homeline\nNgười đặt hàng (Purchaser)\nTrần Thành Tiền\nSố điện thoại\n02471066866\nEmail\ngd.vpp.1511@winmart.masangroup\n.com\nNgày giao (Delivery Date)\n11.03.2023\nGhi chú"\r\n"Nhà cung cấp (Supplier): 0002002695\nCTY TNHH MTV TMDV THIÊN LONG HOÀN C\n658P - 658R,đường Phạm Văn Chí ,Phường 08, Quận 6,",""\r\n',
					'"Stt\nTên hàng\nMã vạch\nSố lượng\nĐVT\nĐơn giá\nThành tiền (VND)\n(No.)\n(Item Description)\n(Barcode)\n(Quantity)\n(Unit)\n(Unit Price)\n(Amount)"\r\n"10\n10227054\n8935001804369\n50\nVI\n11,780\n589,000\nTL Vỉ 2 Bút Bi Laris Tl095"\r\n"20\n10227055\n8935001804420\n30\nVI\n11,025\n330,750\nTL Vỉ 5 Bút Bi Tl-097"\r\n"30\n10033597\n8935001801580\n30\nVI\n8,440\n253,200\nTL Vỉ 2 bút bi TL025"\r\n"40\n10033598\n8935001801610\n50\nVI\n14,130\n706,500\nTL Vỉ 5 bút bi TL027"\r\n"50\n10033602\n8935001801702\n20\nVI\n17,880\n357,600\nTL Vỉ 2 bút bi TL036"\r\n"60\n10033625\n8935001850380\n30\nVI\n11,000\n330,000\nTL Vỉ 2 Bút GEL012/DO"\r\n"70\n10033763\n8935001806486\n50\nVI\n15,700\n785,000\nTL Vỉ 5 bút bi TL079"\r\n"80\n10033791\n8935001803980\n30\nVI\n7,950\n238,500\nTL Vỉ 3 bút bi TL089"\r\n"90\n10033797\n8935001807834\n30\nVI\n14,850\n445,500\nTL Vỉ 3 Bút lông kim FL04/DO"\r\n"100\n10227074\n8935001848080\n20\nVI\n5,000\n100,000\nTL Vỉ 2 Chuốt Bút Chì S-09"\r\n"110\n10033621\n8935001821144\n10\nVI\n8,748\n87,480\nTL Vỉ 3 Bút chì gỗ GP-04"\r\n"120\n10033623\n8935001821069\n10\nVI\n7,695\n76,950\nTL Vỉ 3 Bút chì khúc PC-09"\r\n"130\n10227062\n8935001813460\n20\nCAI\n8,440\n168,800\nTL Bút Dạ Quang Hl-012"\r\n',
					'"Stt\nTên hàng\nMã vạch\nSố lượng\nĐVT\nĐơn giá\nThành tiền (VND)\n(No.)\n(Item Description)\n(Barcode)\n(Quantity)\n(Unit)\n(Unit Price)\n(Amount)"\r\n"140\n10033615\n8935001811213\n20\nVI\n9,420\n188,400\nTL Vỉ 2 Bút lông bảng WB02"\r\n"150\n10033616\n8935001815372\n10\nVI\n11,124\n111,240\nTL Vỉ 2 Bút lông bảng WB03"\r\n"160\n10034120\n8935001846888\n10\nZBO\n14,230\n142,300\nTL Bộ sáp và Tập 12 GS-015"\r\n"170\n10034032\n8935001846352\n60\nCAI\n4,970\n298,200\nTL Keo khô G-014"\r\n"180\n10315837\n8935001847816\n20\nZBO\n26,980\n539,600\nTL Bộ sáp nặn MCT - C01/DO - 16 món"\r\n"190\n10316863\n8935001848691\n20\nVI\n6,354\n127,080\nTL Vỉ 2 Gôm E-017/FR"\r\n"200\n10208858\n8935001840145\n20\nCAI\n3,330\n66,600\nTL Thước Thẳng 20Cm Sr-02"\r\n"210\n10033653\n8935001840138\n10\nCAI\n4,350\n43,500\nTL Thước thẳng 30cm SR-03"\r\n"220\n10200594\n8935001843221\n30\nCUO\n29,450\n883,500\nTL Băng Keo Bkt-15"\r\n"230\n10034488\n8935001841456\n10\nCUO\n15,012\n150,120\nTL Băng keo OPP trong BKT-08"\r\n"240\n10034489\n8935001841470\n10\nCUO\n18,549\n185,490\nTL Băng keo OPP trong BKT-10"\r\n"250\n10035140\n8936040451750\n30\nCAI\n3,530\n105,900\nTL Bìa nút A4 có in - HCB2434 loại 1"\r\n"260\n10280962\n8935283900162\n20\nCAI\n31,800\n636,000\nTL Kéo văn phòng SC-016 hộp 20/T240"\r\n"270\n10280957\n8935001839347\n10\nRAM\n73,500\n735,000\nTL Giấy photo A4 70 PP-001"\r\n"280\n10208768\n8935001845836\n50\nQUY\n5,650\n282,500\nTL Tập Nb52 Điểm 10 (96T-4 OLy)"\r\n"290\n10208773\n8935001845591\n50\nQUY\n6,020\n301,000\nTL Tập Nb44 Điểm 10 (96T-4 Ô Ly Vuông)"\r\n"300\n10034029\n8935001845454\n50\nQUY\n5,190\n259,500\nTL Tập NB39 Điểm 10 (96T-4 oly)"\r\n"Tổng giá trị trước thuế (Total excl VAT amount)\n9,525,210\n- 5% (VAT)\n5,505\n- 8% (VAT)\n0\n- 10% (VAT)\n941,511\n- 15% (VAT)\n0\nThuế GTGT (VAT Amount)\n947,016\nTổng giá trị đơn hàng (Total amount)\n10,472,226"\r\n',
				],
                form_filter: {
                    method: [],
                    camelotFlavor: [],
                }
			};
		},
	};
</script>

<style>
	.container-rate {
		width: 100%;
		padding: 1.5rem;
	}

	.card-rate {
		border-radius: 0.5rem;
		width: 100%;
		display: flex;
		gap: 1px;
		flex-direction: column;
		padding: 1rem 0rem;
		box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
		background-color: #fff;
	}

	.box-rate {
		display: flex;
		width: 100%;
		gap: 1px;
		margin-bottom: 1rem;
	}

	.review-content {
		width: 100%;
		box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
		padding: 0 0.5rem;
		border-radius: 0.5rem;
		height: fit-content;
		min-height: 5rem;
	}

	.rate {
		display: flex;
		gap: 0.5rem;
		flex-wrap: wrap;
		flex-direction: column;
		width: 30%;
	}
	.line {
		border: 1px solid black;
		opacity: 0.1;
	}

	.criteria {
		padding: 0.3rem;
		background-color: rgb(23, 162, 184);
		color: white;
		border-radius: 10px !important;
		margin-right: 10px !important;
		display: inline-block;
		font-size: 12px;
		font-weight: 700;
		text-align: center;
	}
	.time {
		color: #999;
		display: flex;
		justify-content: flex-start;
		font-size: 15px;
		margin-left: 0.5rem;
		align-items: center;
	}
	.box-images {
		display: flex;
		flex-wrap: wrap;
		width: 100%;
	}
	.first-phase-result {
		height: 100vh;
		overflow-y: scroll;
		padding: 0 10px 0 10px;
		/* background-color: #fff; */
		border-radius: 10px;
	}

    .form-check-input {
        margin-left: 30px;
    }
    .form-number-input{
        border: 1px solid #dbd5d5
    }
</style>
