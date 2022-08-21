<template>
    <section>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Product Name</label>
                            <input type="text" v-model="product_name" placeholder="Product Name" class="form-control">
                            <p v-if="errors.title" class="invalid-feedback d-block">{{errors.title[0]}}</p>
                        </div>
                        <div class="form-group">
                            <label for="">Product SKU</label>
                            <input type="text" v-model="product_sku" placeholder="Product Name" class="form-control">
                            <p v-if="errors.sku" class="invalid-feedback d-block">{{errors.sku[0]}}</p>
                        </div>
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea v-model="description" id="" cols="30" rows="4" class="form-control"></textarea>
                            <p v-if="errors.description" class="invalid-feedback d-block">{{errors.description[0]}}</p>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                    </div>
                    <div class="card-body border">
                        <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions" @vdropzone-success="uploadSuccess"></vue-dropzone>

                    </div>


                </div>
                <div class="row">
                    <div v-for="(product_img, index) in product_images" class="col-3" style="position: relative">
                        <img :src="product_img.full_url" class="img-thumbnail" alt="">
                        <button @click="deleteImage(product_img, index)" class="btn btn-sm btn-outline-danger" style="position: absolute;top: 24px; left: 10%">X</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Variants</h6>
                    </div>
                    <div class="card-body">
                        <div class="row" v-for="(item,index) in product_variant">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Option</label>
                                    <select v-model="item.option" class="form-control">
                                        <option v-for="variant in variants"
                                                :value="variant.id">
                                            {{ variant.title }}
                                        </option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label v-if="product_variant.length != 1" @click="product_variant.splice(index,1); checkVariant"
                                           class="float-right text-primary"
                                           style="cursor: pointer;">Remove</label>
                                    <label v-else for="">.</label>
                                    <input-tag v-model="item.tags" @input="checkVariant" class="form-control"></input-tag>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer" v-if="product_variant.length < variants.length && product_variant.length < 3">
                        <button @click="newVariant" class="btn btn-primary">Add another option</button>
                        <p v-if="errors.product_variant" class="invalid-feedback d-block">{{errors.product_variant[0]}}</p>

                    </div>

                    <div class="card-header text-uppercase">Preview</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>Variant</td>
                                    <td>Price</td>
                                    <td>Stock</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="variant_price in product_variant_prices">
                                    <td>{{ variant_price.title }}</td>
                                    <td>
                                        <input type="text" class="form-control" v-model="variant_price.price">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="variant_price.stock">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <p v-if="errors.product_variant_prices" class="invalid-feedback d-block">{{errors.product_variant_prices[0]}}</p>

                    </div>
                </div>
            </div>
        </div>
        <div v-if="status_description" class="alert" :class="alert_class" role="alert">
            {{ status_description }}
        </div>
        <button v-if="is_save" @click="saveProduct" type="submit" class="btn btn-lg btn-primary">Save</button>
        <button v-if="!is_save" @click="updateProduct" type="submit" class="btn btn-lg btn-primary">Update</button>
        <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
    </section>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import InputTag from 'vue-input-tag'

export default {
    components: {
        vueDropzone: vue2Dropzone,
        InputTag
    },
    props: {
        variants: {
            type: Array,
            required: true
        },
        product : {
            type:Object
        },
        product_variants : {
            type:Array
        },
        product_variant_price :{
            type:Array
        },
        product_image_list:{
            type:Array
        }
    },
    data() {
        return {
            product_id: '',
            product_name: '',
            product_sku: '',
            description: '',
            images: [],
            product_variant: [
                {
                    option: this.variants[0].id,
                    tags: []
                }
            ],
            product_variant_prices: [],
            product_images:[],
            dropzoneOptions: {
                url: '/upload',
                thumbnailWidth: 150,
                maxFilesize: 0.5,
                headers: {
                    "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content
                }
            },
            errors : {},
            status_code : '',
            status_description:'',
            alert_class:'alert-success',
            is_save:true
        }
    },
    methods: {
        // it will push a new object into product variant
        newVariant() {
            let all_variants = this.variants.map(el => el.id)
            let selected_variants = this.product_variant.map(el => el.option);
            let available_variants = all_variants.filter(entry1 => !selected_variants.some(entry2 => entry1 == entry2))
            // console.log(available_variants)

            this.product_variant.push({
                option: available_variants[0],
                tags: []
            })
        },

        // check the variant and render all the combination
        checkVariant() {
            let tags = [];
            this.product_variant_prices = [];
            this.product_variant.filter((item) => {
                tags.push(item.tags);
            })

            this.getCombn(tags).forEach(item => {
                this.product_variant_prices.push({
                    title: item,
                    price: 0,
                    stock: 0
                })
            })
        },

        // combination algorithm
        getCombn(arr, pre) {
            pre = pre || '';
            if (!arr.length) {
                return pre;
            }
            console.log(arr[0]);
            let self = this;
            let ans = arr[0].reduce(function (ans, value) {
                return ans.concat(self.getCombn(arr.slice(1), pre + value + '/'));
            }, []);
            return ans;
        },

        // store product into database
        saveProduct() {
            let product = this.getProduct()

            this.errors = {};
            axios.post('/product', product).then(response => {
                this.status_description = response.data.status_description;
                this.alert_class ='alert-success';
                window.setTimeout(function() {
                    location.reload();
                }, 3000);
            }).catch(error => {
                this.errors = error.response.data.errors;
                this.alert_class ='alert-danger'
                this.status_description =error.response.data.status_description

            })

        },
        uploadSuccess(file, response) {
            this.images.push(response.data.path)
            console.log(this.images)
        },
        getProduct(){
            return {
                title: this.product_name,
                sku: this.product_sku,
                description: this.description,
                product_image: this.images,
                product_variant: this.product_variant,
                product_variant_prices: this.product_variant_prices
            }
        },
        deleteImage(product_img, index){
            console.log(product_img, this.product_images);
            axios.post('/delete/product/image/'+product_img.id).then(response => {
                this.status_description = response.data.status_description;
                this.alert_class ='alert-success';
                this.product_images.splice(index, 1);
            }).catch(error => {
                this.alert_class ='alert-danger'
                this.status_description =error.response.data.status_description

            })
        },
        updateProduct(){
            let product = this.getProduct()

            this.errors = {};
            axios.put('/product/'+this.product_id, product).then(response => {
                this.status_description = response.data.status_description;
                this.alert_class ='alert-success';
                window.setTimeout(function() {
                    location.reload();
                }, 3000);
            }).catch(error => {
                this.errors = error.response.data.errors;
                this.alert_class ='alert-danger'
                this.status_description =error.response.data.status_description

            })
        }


    },
    mounted() {

        let props = this.$props;
        let productObj = props?.product;

        if (productObj){
            this.product_id = productObj.id;
            this.product_name =productObj.title;
            this.product_sku =productObj.sku;
            this.description =productObj.description;
            this.product_variant = props?.product_variants;
            this.product_variant_prices = props?.product_variant_price;
            this.product_images = props?.product_image_list
            this.is_save = false
        }

        console.log('Component mounted.')
    }
}
</script>
