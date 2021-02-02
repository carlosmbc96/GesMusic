<template></template>

<script>
export default {
  props: ["content"],
  created() {
    this.$info({
      title: "Ayuda del Sistema!",
      content: this.content,
      okText: "Continuar",
    });
  },
};
</script>

<style>
</style>