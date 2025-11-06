var loader = {
    template: `
      <div class="loader" v-if="visible">
        <div class="spinner"></div>
      </div>
    `,
    props: {
      visible: { type: Boolean, default: false }
    }
  };
  