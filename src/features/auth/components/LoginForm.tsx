import { AxiosError } from "axios";
import { useState } from "react";
import { Button } from "../../../components/ui/button";
import { Input } from "../../../components/ui/input";
import { isValidEmail } from "../../../lib/validators";
import { useLogin } from "../hooks/useLogin";

type LoginErrorResponse = {
  message?: string;
  errors?: Record<string, string[]>;
};

export function LoginForm() {
  const [email, setEmail] = useState("admin@absensi.local");
  const [password, setPassword] = useState("password123");
  const mutation = useLogin();

  const canSubmit = isValidEmail(email) && password.length >= 6 && !mutation.isPending;

  const errorText = (() => {
    if (!(mutation.error instanceof AxiosError)) {
      return null;
    }

    const payload = mutation.error.response?.data as LoginErrorResponse | undefined;

    if (payload?.errors?.email?.[0]) {
      return payload.errors.email[0];
    }

    return payload?.message ?? mutation.error.message;
  })();

  return (
    <form
      className="space-y-4"
      onSubmit={(event) => {
        event.preventDefault();
        if (!canSubmit) {
          return;
        }
        mutation.mutate({ email, password });
      }}
    >
      <div className="space-y-2">
        <label className="text-sm text-muted-foreground">Email</label>
        <Input type="email" value={email} onChange={(event) => setEmail(event.target.value)} />
      </div>
      <div className="space-y-2">
        <label className="text-sm text-muted-foreground">Password</label>
        <Input type="password" value={password} onChange={(event) => setPassword(event.target.value)} />
      </div>
      <Button className="w-full" disabled={!canSubmit}>
        {mutation.isPending ? "Signing in..." : "Sign In"}
      </Button>
      {errorText ? <p className="text-sm text-red-500">{errorText}</p> : null}
    </form>
  );
}
