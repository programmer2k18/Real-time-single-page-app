<template>
    <v-container>
        <ValidationObserver ref="observer" v-slot="{ validate, reset }">
            <form>

                <ValidationProvider v-slot="{ errors }" name="title" rules="required">
                    <v-text-field
                            v-model="title"
                            :error-messages="errors"
                            label="Title"
                            type="text"
                            required
                    ></v-text-field>
                </ValidationProvider>


                <ValidationProvider v-slot="{ errors, valid }"  name="category">
                    <v-select

                            v-model="category_id"
                            :items="categories"
                            item-text="name"
                            item-value="id"
                            :error-messages="errors"
                            label="Category"
                            autocomplete

                    ></v-select>

                </ValidationProvider>


                <ValidationProvider v-slot="{ errors }" name="body" rules="required">
                    <v-text-field
                            v-model="body"
                            :error-messages="errors"
                            label="Description"
                            type="textarea"
                            required
                    ></v-text-field>
                </ValidationProvider>



                <v-btn class="mr-4" @click="update" color="green">Save</v-btn>
                <v-btn @click="clear">Cancel</v-btn>
            </form>
        </ValidationObserver>
    </v-container>
</template>

<script>
    import { required, email, max } from 'vee-validate/dist/rules'
    import { extend, ValidationObserver, ValidationProvider, setInteractionMode } from 'vee-validate'
    import axios from 'axios';
    setInteractionMode('eager')

    extend('required', {
        ...required,
        message: '{_field_} can not be empty',
    })

    extend('max', {
        ...max,
        message: '{_field_} may not be greater than {length} characters',
    })

    export default {
        components: {
            ValidationProvider,
            ValidationObserver,
        },
        data: () => ({
            title: '',
            body: '',
            category_id: null,
            categories:{},
            errors:{},
            question:{}
        }),

        created(){

            axios.get('/api/question/'+this.$route.params.questionSlug)
                .then(res => {
                    this.question = res.data;
                    this.title = this.question.title;
                    this.body = this.question.body;
                })
                .catch(error=>alert('Something went wrong, Please try again'));

            axios.get('/api/category')
                .then(res => this.categories = res.data)
                .catch(error => alert('Something went wrong..'));

        },

        methods: {

            update () {
                this.$refs.observer.validate();
                let questData = {'title':this.title,'body':this.body,'category_id':this.category_id}
                axios.patch('/api/question/'+this.question.path.split('/')[1],questData)
                    .then(res => {
                        console.log(res)
                        if(res.status == 200)
                            this.$router.push({name:'forum'})
                        else if (res.status == 206)
                            this.errors = res.data
                    })
                    .catch(error => this.errors = error.response.data.error)
            },


            clear () {
                this.title = '';
                this.body = '';
                this.category_id = null;
            },
        },
    }
</script>