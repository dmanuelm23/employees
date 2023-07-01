<template>
  <GoogleMap api-key="AIzaSyB-DlFu4I63lUpXonx23PWL-39C4GafBRM" style="width: 100%; height: 500px" :center="center" :zoom="15">
    <Marker :options="{ position: center }" />
  </GoogleMap>
</template>

<script>
import { GoogleMap, Marker } from "vue3-google-map";
export default {
  components: { GoogleMap, Marker },
    props: {
        latitude:{
            type:Number,
            required: true,
        },
        longitude:{
            type: Number,
            required: true,
        },

    },
   data() {
      return {
        center:{lat:0,lng:0},
      }
    },
    mounted() {
      this.center.lat = this.latitude;
      this.center.lng = this.longitude;
    } 
}

</script>