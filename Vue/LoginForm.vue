<template>
  <div>
    <form @submit.prevent="submitForm">
      <div v-if="failed">
        <p>{{ $t('login.login_error') }}</p>
      </div>
      <div>
        <label for="email">{{ $t('login.email') }}:</label>
        <input v-model="user.email" id="email" type="email" required>
      </div>
      <div>
        <label for="password">{{ $t('login.password') }}:</label>
        <input v-model="user.password" id="password" type="password" required>
      </div>
      <button type="submit">{{ $t('login.submit') }}</button>
    </form>
  </div>
</template>

<script>
import '@/assets/css/formStyles.css';
import { login } from '@/services/userService';
import router from '@/router';

export default {
  data() {
    return {
      user: {
        email: '',
        password: ''
      },
      failed: false
    }
  },
  methods: {
    async submitForm() {
      this.failed = false;
      try {
        const responseData = await login(this.user);
        localStorage.setItem('accessToken', responseData.accessToken);
        await router.push('/success');
      } catch (error) {
        this.failed = true;
      }
    }
  }
}
</script>