<template>
  <div>
    <footer>
      <div class="footer justify-content-center d-flex">
        <p class="pt-2"> {{ footerMessage }}</p>
      </div>

    </footer>
  </div>

</template>

<script>
export default {
  name:'Footer',
  props: {
    footerMessage: {
      type: String,
      required: true,

    }
  }
}

</script>