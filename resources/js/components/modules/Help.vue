<template></template>

<script>
export default {
  props: ["content", "type"],
  created() {
    if (this.type === "info") {
      this.$info({
        title: "Ayuda del Sistema!",
        content: this.content,
        okText: "Continuar",
        onOk: () => {
          this.$emit("close");
        },
      });
    } else {
      this.$warning({
        title: "Ayuda del Sistema!",
        content: this.content,
        okText: "Continuar",
        onOk: () => {
          this.$emit("close");
        },
      });
    }
  },
};
</script>

<style></style>
