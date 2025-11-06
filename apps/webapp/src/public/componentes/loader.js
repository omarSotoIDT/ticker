var loader = {
    props: {
        visible: {
            type: Boolean,
            default: false
        }
    },
    template: `
      <div v-if="visible" class="loader-overlay">
        <div class="loader-spinner"></div>
      </div>
    `
};
