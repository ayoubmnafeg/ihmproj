import { AuthShell } from "@/components/auth/auth-shell";
import { RegisterForm } from "@/components/auth/register-form";

export default function RegisterPage() {
  return (
    <AuthShell
      mode="register"
      title="Create your account"
      subtitle="Registration should immediately authenticate the new user."
    >
      <RegisterForm />
    </AuthShell>
  );
}
