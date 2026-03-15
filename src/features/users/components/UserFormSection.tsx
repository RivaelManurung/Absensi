import { Input } from "../../../components/ui/input";

type UserFormSectionProps = {
  name: string;
  email: string;
  password: string;
  isActive: boolean;
  onChange: (next: { name?: string; email?: string; password?: string; isActive?: boolean }) => void;
};

export function UserFormSection({ name, email, password, isActive, onChange }: UserFormSectionProps) {
  return (
    <div className="grid gap-4 md:grid-cols-2">
      <Input placeholder="Name" value={name} onChange={(event) => onChange({ name: event.target.value })} />
      <Input placeholder="Email" type="email" value={email} onChange={(event) => onChange({ email: event.target.value })} />
      <Input
        placeholder="Password"
        type="password"
        value={password}
        onChange={(event) => onChange({ password: event.target.value })}
      />

      <div>
        <p className="mb-2 text-sm text-muted-foreground">Status</p>
        <div className="flex gap-4 text-sm">
          <label className="inline-flex items-center gap-2">
            <input type="radio" name="status" checked={isActive} onChange={() => onChange({ isActive: true })} /> Active
          </label>
          <label className="inline-flex items-center gap-2">
            <input type="radio" name="status" checked={!isActive} onChange={() => onChange({ isActive: false })} /> Inactive
          </label>
        </div>
      </div>
    </div>
  );
}
