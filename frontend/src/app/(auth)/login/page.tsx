import { AuthShell } from "@/components/auth/auth-shell";
import { LoginForm } from "@/components/auth/login-form";

export default function LoginPage() {
  return (
    <AuthShell
      mode="login"
      title="Welcome back"
      subtitle="Log in with the Laravel account you already have."
    >
      <LoginForm />
    </AuthShell>
  );
}
