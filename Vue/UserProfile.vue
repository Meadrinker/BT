<template>
  <div v-if="user">
    <h2>{{ $t('profile.user_data') }}:</h2>
    <p>{{ $t('profile.email') }}: {{ user.email }}</p>
    <p>{{ $t('profile.first_name') }}: {{ user.firstName }}</p>
    <p>{{ $t('profile.last_name') }}: {{ user.lastName }}</p>
  </div>
  <div v-else>
    <p>{{ $t('profile.loading_user_data') }}</p>
  </div>
</template>

<script>
import { fetchUserData } from '@/services/userService';

export default {
  data() {
    return {
      user: null,
    }
  },
  props: {
    accessToken: String,
  },
  async created() {
    try {
      const userData = await fetchUserData(this.accessToken);
      this.user = userData;
    } catch (error) {
      console.error(error);
    }
  },
}
</script>