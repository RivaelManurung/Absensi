import { LoginForm } from "../components/LoginForm";

export function LoginPage() {
  return (
    <div className="mx-auto flex min-h-screen max-w-md items-center px-4">
      <section className="w-full rounded-2xl border border-border bg-card p-6 shadow-xl">
        <h1 className="mb-1 text-2xl font-semibold">Welcome Back</h1>
        <p className="mb-6 text-sm text-muted-foreground">Sign in to continue to your dashboard.</p>
        <p className="mb-4 rounded-xl border border-border bg-muted/30 px-3 py-2 text-xs text-muted-foreground">
          Default seeder account: admin@absensi.local / password123
        </p>
        <LoginForm />
      </section>
    </div>
  );
}
