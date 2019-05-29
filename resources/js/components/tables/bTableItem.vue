<template>
    <tr>
        <td v-if="params.pagination">{{keys + paginate.from}}</td>
        <td v-else>{{keys + 1}}</td>
        <td v-for="field in params.fields" :key="field.id" v-if="field.edit == true"> 
            <div v-for="(value, key) in item" :key="value.id">
                <div v-if="field.input == 'images' && field.key == key" v-bind:class="field.key == key && field.edit == false ? false : itemClass">
                    <div class="" v-for="img in value" :key="img.id">
                        <img :src="'/storage/' + img.url" alt="" style="width:200px">
                        <button type="button" class="close" aria-label="Close" v-on:click="removeImg(img)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div v-else>
                    <div v-bind:class="field.key == key && field.edit == false ? false : itemClass" v-if="field.key == key">{{value}}</div>
                </div>
                <div v-bind:class="itemEditClass">
                    <div class="form-wrap">
                        <div v-if="field.input == 'input'">
                            <input :type="field.type" class="form-control" v-model="item[key]" v-if="field.key == key && field.edit == true">
                        </div>
                        <div v-if="field.input == 'textarea'">
                            <textarea :type="field.type" class="form-control" v-model="item[key]" v-if="field.key == key && field.edit == true"></textarea>
                        </div>
                        <div v-if="field.input == 'images'">
                            <input :type="field.type" name="file[]" accept="image/*" v-if="field.key == key && field.edit == true" hidden>
                            <button class="custom-upload" v-if="field.key == key && field.edit == true" @click="handleFileUpload()">Add File</button>
                            <ul class="files" v-if="field.key == key && field.edit == true">
                                <li v-for="(file, index) in files" :key="index">
                                    <button @click="removeFile(index)">
                                    x
                                    </button>
                                    {{ file.name }}
                                </li>
                            </ul>
                        </div>
                        <input type="hidden" class="form-control" v-model="item[key]" v-else>
                    </div>
                </div>   
            </div>
        </td>
        <td>
            <button class="btn btn-primary" v-on:click="editShowItem()" v-bind:class="isActive">
                {{btnEditText}}
            </button>
            <button class="btn btn-danger" v-on:click="remove(item.id)">
                <!-- <span class="spinner-border spinner-border-sm" :class="itemRemoveClass" role="status" aria-hidden="false"></span> -->
                Удалить
            </button>
        </td>
    </tr>
</template>
<script>
export default {
    props: {
        item: Object,
        params: Object,
        keys: Number,
        sorting: Object,
        paginate: Object
    },
    data: () => ({
        isButtonDisabled: false,
        isActive: false,
        itemClass: false,
        formData: new FormData(),
        itemEditClass: "d-none",
        itemRemoveClass: "d-none",
        btnEditText: "Редактировать",
        files: [],
        imagePrev: [],
        input: null
    }),
    watch: {
        files(files) {
            console.log(files);
            // this.$emit('input', files)
        }
    },
    methods: {
        remove(id) {
            this.$store.dispatch('remove', {url: this.params.baseUrl + "/" + id, method: "delete"});
        },
        removeImg(data) {
            axios.post('api/files/remove', data)
            .then(
                res => {
                    this.$store.dispatch('save', {url: this.params.baseUrl, method: "delete", list: this.item, images: res.data.data.id});
                }
            ).catch(
                error => {
                    console.log(error);
                }
            );
        },
        async edit() {
            if(this.files.length > 0) {
                this.files.forEach(files => {
                    this.formData.append('file[]', files);
                });
                await axios.post('api/files', this.formData, {
                    headers: {'Content-Type': 'multipart/form-data'}
                })
                .then(
                    res => {
                        // очищать input после загрузки файла
                        this.$store.dispatch('save', {url: this.params.baseUrl, method: "put", list: this.item, images: res.data.files}).then(() => {
                                this.files = [];
                                // var newFileList = Array.from(this.input.files);
                                // newFileList.splice(0,1);
                                // console.log(Array.from(this.input.files).pop());
                                // console.log(Array.from(this.input.files).length);
                                // while (Array.from(this.input.files).length) {
                                //     Array.from(this.input.files[0]).pop();
                                // }
                                // Array.from(this.input.files).forEach((element, key) => {
                                //     console.log(this.input.files[key]);
                                // });
                            }
                        );
                    }
                ).catch(
                    error => {
                        console.log(error);
                    }
                );
            } else {
                await this.$store.dispatch('save', {url: this.params.baseUrl, method: "put", list: this.item});
            }
        },
        handleFileUpload() {
            const event = new MouseEvent('click', {
                'view': window,
                'bubbles': true,
                'cancelable': true
            });
            this.input.dispatchEvent(event);
            // this.input.addEventListener('change', () => this.onFileSelection())
            // this.input.style = 'none'      
            // console.log(this.$el);
            //  Array.from(event.target.files);
            // console.log(this.$el.querySelector('input[type=file]'));
            // this.$el.querySelector('input[type=file]');
            // this.$emit('input', []);
            // this.files = this.$el.querySelector('input[type=file]');
            // console.log(this.$el.querySelector('input[type=file]').files);
            // this.files = Array.from(event.target.files);
            // console.log(Array.from(event.target.files);
            // this.files.value = '';
            // let vm = this;
            // await Array.from(this.files).forEach(img => {
            //     const reader = new FileReader();
            //     reader.onload = (e) => {
            //         vm.imagePrev.push(e.target.result);
            //     }
            //     reader.readAsDataURL(img);
            // });
        },
        onFileSelection() {
            for (let file of this.input.files) {
                this.files.push(file);
            }
             this.input.value = null;
        },
        removeFile(index) {
            this.files.splice(index, 1);
        },
        editShowItem() {
            this.isActive = !this.isActive;
            if(this.isActive) {
                this.itemClass = 'd-none';
                this.itemEditClass = false;
                this.btnEditText = "Сохранить"
            } else {
                this.itemClass = false;
                this.btnEditText = "Редактировать";
                this.itemEditClass = 'd-none';
                this.edit();
            }
        }
    },
    mounted() {
        this.input = this.$el.querySelector('input[type=file]');
        this.input.addEventListener('change', () => this.onFileSelection())
        this.input.style.display = 'none'
        
        // Set multiple attribute on input, if max is more than one
        if (this.max > 1) {
        this.input.setAttribute('multiple', 'multiple');
        } else {
        this.input.removeAttribute('multiple');
        }
        
        // Populate internal value, if external value is given,
        // attempt to populate external value by firing event if not
        if (this.value) {
            this.files = this.value
        } else {
            this.$emit('input', [])
        } 
        if (this.value) {
            this.files = this.value
        } else {
            this.$emit('input', [])
        }
    }
}
</script>