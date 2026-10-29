"use client";

import { useMemo, useState } from "react";
import {
  ArrowUpRight,
  BriefcaseBusiness,
  Building2,
  ExternalLink,
  FileText,
  Globe,
  Headphones,
  LayoutGrid,
  Search,
  Users,
  X,
} from "lucide-react";

const categories = [
  { name: "Semua Link", icon: LayoutGrid },
  { name: "Operasional", icon: BriefcaseBusiness },
  { name: "Dokumen", icon: FileText },
  { name: "SDM", icon: Users },
  { name: "Sistem Internal", icon: Globe },
  { name: "Dukungan", icon: Headphones },
];

const links = [
  {
    name: "Portal Operasional",
    description: "Portal Operasional",
    category: "Operasional",
    url: "https://portal.operasional.com",
    initials: "OP",
    color: "bg-blue-100 text-blue-700",
    tag: "Operasional",
  },
  {
    name: "Dokumen Perusahaan",
    description: "Temukan dokumen, panduan, dan referensi kerja",
    category: "Dokumen",
    url: "https://dokumen.perusahaan.com",
    initials: "DP",
    color: "bg-violet-100 text-violet-700",
    tag: "Dokumen",
  },
  {
    name: "Portal Karyawan",
    description: "Informasi Portal Karyawan",
    category: "SDM",
    url: "https://portal.karyawan.com",
    initials: "PK",
    color: "bg-emerald-100 text-emerald-700",
    tag: "SDM",
  },
  {
    name: "Sistem Informasi",
    description: "Akses aplikasi dan sistem internal perusahaan.",
    category: "Sistem Internal",
    url: "https://sistem.internal.com",
    initials: "SI",
    color: "bg-cyan-100 text-amber-700",
    tag: "Sistem Internal",
  },
  {
    name: "Layanan IT",
    description: "Pusat bantuan teknis dan pelaporan kendala.",
    category: "Dukungan",
    url: "https://layanan.it.com",
    initials: "IT",
    color: "bg-rose-100 text-rose-700",
    tag: "Dukungan",
  },
  {
    name: "Dashboard Bisnis",
    description: "Ringkasan informasi dan data untuk kebutuhan kerja",
    category: "Operasional",
    url: "https://dashboard.bisnis.com",
    initials: "DB",
    color: "bg-cyan-100 text-amber-700",
    tag: "Operasional",
  },
];

export default function Home() {
  const [search, setSearch] = useState("");
  const [activeCategory, setActiveCategory] = useState("Semua Link");

  const filteredLinks = useMemo(() => {
    const query = search.toLowerCase();

    return links.filter((link) => {
      const matchesCategory =
        activeCategory === "Semua Link" || link.category === activeCategory;
      const matchesSearch =
        link.name.toLowerCase().includes(query) ||
        link.description.toLowerCase().includes(query);

      return matchesCategory && matchesSearch;
    });
  }, [search, activeCategory]);

  return (
    <main className="min-h-screen bg-slate-50 text-slate-800">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
          <a href="#" className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-700 text-white">
              <Building2 size={22} />
            </div>
            <div>
              <p className="font-bold tracking-tight text-slate-900">
                Company Portal
              </p>
              <p className="text-xs text-slate-500">Pusat akses perusahaan</p>
            </div>
          </a>

          <div className="hidden items-center gap-2 text-sm font-medium text-slate-500 sm:flex">
            <span className="h-2 w-2 rounded-full bg-emerald-500" />
            Pusat tautan internal
          </div>
        </div>
      </header>

      <section className="relative overflow-hidden bg-white">
        <div className="pointer-events-none absolute -left-20 bottom-0 h-52 w-52 rounded-full bg-indigo-100/50 blur-3xl" />

        <div className="relative mx-auto max-w-7xl px-5 pb-12 pt-14 sm:px-8 sm:pb-16 sm:pt-20">
          <div className="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-800">
            <Globe size={14} />
            SATU PORTAL, AKSES SEMUA
          </div>

          <h1 className="text-4xl font-bold leading-tight text-slate-950 sm:text-5xl lg:text-6xl">
            Semua akses,
            <span className="block text-blue-700">satu tempat.</span>
          </h1>

          <p className="mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg">
            Temukan website, aplikasi, dan sumber daya perusahaan dengan lebih cepat.
            Semua tautan penting tersedia dalam satu portal.
          </p>

          <div className="mt-8 flex max-w-2xl items-center gap-3 rounded-2xl border border-slate-200 bg-white p-2 shadow-lg shadow-slate-200/60 transition focus-within:ring-4 focus-within:ring-blue-100">
            <Search className="ml-3 shrink-0 text-slate-400" size={22} />
            <input
              type="search"
              value={search}
              onChange={(event) => setSearch(event.target.value)}
              placeholder="Cari website, aplikasi, atau kategori"
              aria-label="Cari tautan"
              className="min-w-0 flex-1 bg-transparent py-3 text-slate-700 outline-none placeholder:text-slate-400 sm:text-base"
            />
            {search && (
              <button
                type="button"
                onClick={() => setSearch("")}
                className="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                aria-label="Hapus pencarian"
              >
                <X size={18} />
              </button>
            )}
          </div>

          <p className="mt-3 text-xs text-slate-500">
            Tips: cari berdasarkan nama layanan, fungsi, atau kategori.
          </p>
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-5 py-10 sm:px-8">
        <div className="mb-8">
          <p className="text-sm font-semibold text-blue-700">DIREKTORI PERUSAHAAN</p>
          <h2 className="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">
            Akses cepat
          </h2>
          <p className="mt-2 text-slate-600">
            Pilih kategori untuk menemukan layanan yang Anda butuhkan.
          </p>
        </div>

        <div className="mb-8 flex flex-wrap gap-2">
          {categories.map((category) => {
            const Icon = category.icon;
            const isActive = activeCategory === category.name;

            return (
              <button
                key={category.name}
                type="button"
                onClick={() => setActiveCategory(category.name)}
                className={`inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition ${
                  isActive
                    ? "bg-blue-700 text-white shadow-md shadow-blue-200"
                    : "border border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:text-blue-700"
                }`}
              >
                <Icon size={17} />
                {category.name}
              </button>
            );
          })}
        </div>

        {filteredLinks.length > 0 ? (
          <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            {filteredLinks.map((link) => (
              <a
                key={link.name}
                href={link.url}
                target="_blank"
                rel="noopener noreferrer"
                className="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg hover:shadow-slate-200/70"
              >
                <div className="flex items-start justify-between gap-4">
                  <div className={`flex h-12 w-12 items-center justify-center rounded-xl text-sm font-bold ${link.color}`}>
                    {link.initials}
                  </div>
                  <ArrowUpRight className="text-slate-400 transition group-hover:text-blue-700" size={20} />
                </div>
                <h3 className="mt-5 text-lg font-bold text-slate-900">{link.name}</h3>
                <p className="mt-2 min-h-12 text-sm leading-6 text-slate-600">{link.description}</p>
                <div className="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                  <span className="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    {link.tag}
                  </span>
                  <span className="inline-flex items-center gap-1 text-xs font-semibold text-blue-700">
                    Buka layanan
                    <ExternalLink size={14} />
                  </span>
                </div>
              </a>
            ))}
          </div>
        ) : (
          <div className="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
            <Search className="mx-auto text-slate-400" size={28} />
            <h3 className="mt-4 font-semibold text-slate-900">Tautan tidak ditemukan</h3>
            <p className="mt-1 text-sm text-slate-500">Coba gunakan kata kunci lain.</p>
          </div>
        )}
      </section>

      <footer className="border-t border-slate-200 bg-white">
        <div className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-6 text-xs text-slate-500 sm:px-8">
          <span>© Company Portal</span>
          <span>Internal use only</span>
        </div>
      </footer>
    </main>
  );
}
