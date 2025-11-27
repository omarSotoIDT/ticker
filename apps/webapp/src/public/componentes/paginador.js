var paginador = {
  template: `
  <div class="pagination-wrapper">
    <div class="pagination" v-if="links && links.length > 3">
      <button
        v-for="(link, index) in translatedLinks"
        :key="index"
        v-html="link.label"
        :disabled="!link.url"
        :class="['page-btn', { active: link.active }]"
        @click="goToPage(link.url)">
      </button>
    </div>
  </div>
  `,
  props: {
    links: { type: Array, default: () => [] }
  },
  computed: {
    // Conservamos la condición de links.length > 3 ya que Anterior y Siguiente son links
    translatedLinks() {
      return this.links.map(link => {
        let newLabel = link.label
          .replace(/&laquo; Previous/i, '← Anterior')
          .replace(/Next &raquo;/i, 'Siguiente →');
        return { ...link, label: newLabel };
      });
    }
  },
  methods: {
    goToPage(url) {
      if (url) this.$emit('navigate', url);
    }
  }
};
