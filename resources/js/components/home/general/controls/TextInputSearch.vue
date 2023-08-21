<template>
  <div class="parentList">
        <div v-if="!show_combobox">
            <span @click="ShowSearch()"  v-show="!show_search" :title="selectedValue"  style="cursor:pointer;text-align:center" >{{selectedValue}}  
                <!-- <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> -->
                <small><i v-if="show_icon"  :class="icon"></i></small> 
            </span>
            <div class="input-group input-group-sm flex-nowrap" v-show="show_search"  >       
                <input ref = "refContent" :required="required" style="min-width: 6rem;"   @click ="getPos()" type="text"  :title="selectedValue"  id="txtContent" name="txtContent"  v-on:keyup="getDataSearch" v-model="selectedValue"    class="form-control form-control-sm" :placeholder="value_placeholder" aria-label="..." aria-describedby="button-addon2">
                <div class="input-group-append">                     
                    <button   @click="clearData" class="btn btn-outline-secondary btn-sm" type="button" id="button-addon2">                       
                        <span aria-hidden="true">×</span>
                    </button>
                    <!-- <button   @click="toggleSearch()" class="btn btn-outline-secondary btn-sm" type="button" id="button-addon2">
                        <i v-if="show_search" class="fas fa-caret-up"></i>
                        <i v-else class="fas fa-caret-down"></i>
                    </button> -->
                </div>
            </div>
        </div>
        <div v-if="show_combobox">
            <div class="input-group input-group-sm flex-nowrap"   >       
                <input ref = "refContent" :required="required"  @click ="getPos()" type="text"   @focus="ShowSearch()"  id="txtContent" name="txtContent" :title="selectedValue" v-on:keyup="getDataSearch" v-model="selectedValue"    class="form-control form-control-sm" :placeholder="value_placeholder" aria-label="..." aria-describedby="button-addon2">
                <div class="input-group-append">                    
                    <button   @click="clearData" class="btn btn-outline-secondary btn-sm" type="button" id="button-addon2">                       
                       <span aria-hidden="true">×</span>
                   </button>
                   <!-- <button   @click="toggleSearch()" class="btn btn-outline-secondary btn-sm" type="button" id="button-addon2">
                       <i v-if="show_search" class="fas fa-caret-up"></i>
                       <i v-else class="fas fa-caret-down"></i>                       
                   </button> -->
                </div>
            </div>
        </div>
        <div class="showList"  v-show="show_search" >
            <b-table    :per-page="perPage" show-empty sticky-header="true"
            @row-clicked="chooseValue" 
            small responsive 
            :busy="loading"
            :items="ListData" 
            :fields="fields">

                <template #empty>
                    <small style="color:gray"><i>Không tìm thấy dữ liệu</i> </small>
                </template>
                <template #table-busy>
                    <!-- <span   class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>   -->
                    <div class="text-center  my-2">
                        <span   class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>  
                    <!-- <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong> -->
                    </div>
                </template>
            </b-table>
        </div>
  </div>
</template>
<script>
  
export default {
    components:{
       
     },
    props: {
       
        fields:Array,
        url:String,
        key_search:String,//sap_code=abc
        search_fix:String,
        key_return:[String, Number],
        other_display:{
            type:String,
            
        },
        required:{
            type:Boolean,
            default:false
        },
        value: [String, Number],
        icon:{
            type:String,
            default:"fas fa-pencil-alt text-gray"
        },
        show_icon:{
            type:Boolean,
            default:false
        },
        show_combobox:{
            type:Boolean,
            default:false
        },
        index:{
            type: [String, Number],
            default:-1
        },
        value_display:{
            type: [String, Number],
            default:""
        },
        value_placeholder:{
            type: [String, Number],
            default:"Chọn..."
        }
        
       
     },   
     
     data() {
        return {
            
            value_return:this.value,
            perPage:10,
            selectedValue:this.value_display,
            show_search:false,
            listComboboxData:[],
            ListData:[],
            token:"",
            page_url_data:this.url,
            left:0,
            top:0,
            loading:false,
            position:0,
            
        }
     },
     created(){
        this.token = "Bearer " + window.Laravel.access_token;
        //this.LoadDataByKey(this.value);
        
    },
     methods:{
        getPos() {
        //  this.left = this.$refs.refContent.getBoundingClientRect().left
        //  this.top = this.$refs.refContent.getBoundingClientRect().top
        //  alert(this.left + '-' + this.top);
        },
        eventReturn(){
            if(this.index === -1 || this.index === ""){
                this.$emit('selectedItem', this.value_return);
               
            }else{
                var data = [];
                data[0] =  this.value_return;
                data[1] = this.index;
                this.$emit('selectedItem',data);
            }
        },
        clearData(){
            this.selectedValue = "";
            this.value_return ="";
           
            this.eventReturn();
            this.HideSearch();
        },
        chooseValue(item){
            let other = (this.other_display == '' || this.other_display == null)?"": (" "+item[this.other_display]);
            this.selectedValue = item[this.key_search] + other ;
            this.value_return = item[this.key_return] ;
            this.listComboboxData.push(item);            
            this.eventReturn();                      
            this.HideSearch();           
        },
        //Tìm kiếm
        getDataSearch(ev){
            if(ev.target.value == "")return;
            var that = this;
           
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            this.timer = setTimeout(() => {
                // your code
                that.loading = true;
                var page_url = that.page_url_data+ "?" +that.key_search+"="+ ev.target.value +"&"+that.search_fix;
            
                fetch(page_url, { headers: { Authorization: that.token } })
                    .then(res => res.json())
                    .then(res => {
                        that.ListData = res.data;
                        that.loading = false;
                     })
                    .catch(err =>{
                        that.loading = false;
                        console.log(err);
                    } );
            }, 800);

           
        },
        LoadDataByKey(key){
            if(key === "" || key === null) return;
           this.loading = true;
           var page_url = this.page_url_data+ "?" +this.key_return+"="+ key ;
           
             fetch(page_url, { headers: { Authorization: this.token } })
                 .then(res => res.json())
                 .then(res => {
                    this.loading = false;
                     this.ListData = res.data;
                     if(this.ListData.length > 0 ){
                        let item = this.ListData[0];
                        console.log(this.other_display);
                        var other = (this.other_display === "" || typeof  this.other_display  === "undefined") ?"": (" "+item[this.other_display]);
                        this.selectedValue = item[this.key_search]+other ;
                     }
                    
 
                 })
                 .catch(err =>{
                    console.log(err);
                    this.loading = false;
                 });
       },
        ShowSearch(event){
            this.show_search = true;
            this.left = this.$refs.refContent.getBoundingClientRect().left
            this.top = this.$refs.refContent.getBoundingClientRect().top
            if(this.value !== "" && this.value !== null ){
                console.log(this.value);
                this.LoadDataByKey(this.value);
            } 
          
        },
        HideSearch(){
             
            this.show_search = false;
        },
        toggleSearch(){
            this.show_search = ! this.show_search ;
        },
        
        //end tìm kiếm

     },
     computed:{
         
     },
     watch:{
        value_display(){
            this.selectedValue = this.value_display;
            
        }
        // value(){
            
        //     if(this.value !== "" && this.value_return != this.value){
        //         this.LoadDataByKey(this.value);
        //     }
            
        // }
     },
}
</script>

<style scoped lang="scss">
 
 .parentList{
      position: relative;
 }
 .showList{
    position: absolute;
    background-color: whitesmoke;
    // display: block;
    // overflow: scroll;
    min-height: 10em;
    max-height: 20em;
    width: auto;
    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
    z-index: 2000;
}
 
</style>
   