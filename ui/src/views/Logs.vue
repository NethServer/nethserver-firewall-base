<template>
  <div>
    <h2>{{$t('logs.title')}}</h2>
  </div>
</template>

<script>
export default {
  name: "Logs",
  data() {
    return {};
  }
};
</script>

<style>
.monospace {
  display: inline;
  padding: 5px;
  font-size: 12px;
}

.logs {
  max-height: 500px;
}
</style>