//Vue.component('message', {
//    template: `
//    <div v-if='isVisible' class="alert alert-success" role="alert"><slot></slot></div>
//    `,
//    data() {
//        return {
//            isVisible: true
//        };
//    },
//});

var $vm = new Vue({

    el: '#root',

    props: ['msg'],

    data:{
        body: '',
    },
    
//    watch: {
//        fgh: function(newVal)
//           this.msg = 'dfvdfdf'
//        }
});

