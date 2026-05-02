import React, { useState } from 'react';
import { 
  Baby, 
  UserX, 
  MapPin, 
  ArrowRightLeft, 
  ArrowLeft,
  Split,
  ChevronRight,
  AlertCircle
} from 'lucide-react';
import { Link } from '@inertiajs/react';

// Import sub-forms
import KematianForm from './Forms/KematianForm';
import KelahiranForm from './Forms/KelahiranForm';
import PindahMasukForm from './Forms/PindahMasukForm';
import PindahKeluarForm from './Forms/PindahKeluarForm';
import PindahRTRWForm from './Forms/PindahRTRWForm';
import PisahKKForm from './Forms/PisahKKForm';

const MUTASI_TYPES = [
  {
    id: 'kematian',
    title: 'Kematian',
    description: 'Warga meninggal dunia (menghapus dari daftar aktif)',
    icon: UserX,
    color: 'red',
  },
  {
    id: 'kelahiran',
    title: 'Kelahiran',
    description: 'Penambahan anggota keluarga baru (bayi lahir)',
    icon: Baby,
    color: 'blue',
  },
  {
    id: 'pindah_masuk',
    title: 'Pindah Masuk',
    description: 'Warga baru pindah ke desa ini (Bisa Tambah Banyak Anggota)',
    icon: MapPin,
    color: 'green',
  },
  {
    id: 'pindah_keluar',
    title: 'Pindah Keluar',
    description: 'Warga pindah ke luar desa/kota/negeri',
    icon: MapPin,
    color: 'orange',
  },
  {
    id: 'pindah_rt_rw',
    title: 'Pindah RT/RW',
    description: 'Satu keluarga pindah alamat di dalam desa',
    icon: ArrowRightLeft,
    color: 'purple',
  },
  {
    id: 'pisah_kk',
    title: 'Pisah KK',
    description: 'Anggota memisahkan diri ke KK baru/lain',
    icon: Split,
    color: 'teal',
  }
];

export default function MutasiFormManager({ wilayahTree, mutasi = null, penduduks = [], masterRwOptions = [] }) {
  const [selectedType, setSelectedType] = useState(mutasi ? mutasi.jenis_mutasi : null);

  if (selectedType) {
    const typeInfo = MUTASI_TYPES.find(t => t.id === selectedType);
    const Icon = typeInfo?.icon || AlertCircle;

    return (
      <div className="flex flex-col h-full animate-in fade-in duration-300">
        {/* Sub-Header / Breadcrumb */}
        <div className="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-white rounded-t-3xl shadow-sm">
          <div className="flex items-center gap-4">
            <div className={`w-12 h-12 rounded-2xl flex items-center justify-center border shadow-sm ${
              selectedType === 'kematian' ? 'bg-red-100 text-red-600 border-red-200' :
              selectedType === 'kelahiran' ? 'bg-blue-100 text-blue-600 border-blue-200' :
              selectedType === 'pindah_masuk' ? 'bg-green-100 text-green-600 border-green-200' :
              selectedType === 'pindah_keluar' ? 'bg-orange-100 text-orange-600 border-orange-200' :
              selectedType === 'pindah_rt_rw' ? 'bg-purple-100 text-purple-600 border-purple-200' :
              'bg-teal-100 text-teal-600 border-teal-200'
            }`}>
              <Icon className="w-6 h-6" />
            </div>
            <div>
              <h3 className="text-xl font-black text-gray-900 tracking-tight uppercase">Mutasi {typeInfo?.title}</h3>
              <button 
                onClick={() => setSelectedType(null)}
                className="text-xs font-bold text-gray-400 hover:text-blue-600 transition-colors flex items-center gap-1 mt-0.5"
              >
                ← Kembali Ganti Jenis Mutasi
              </button>
            </div>
          </div>
        </div>

        {/* Dynamic Form Content */}
        <div className="p-8 bg-white rounded-b-3xl shadow-sm border border-gray-100 border-t-0">
          {selectedType === 'kematian' && <KematianForm mutasi={mutasi} />}
          {selectedType === 'kelahiran' && <KelahiranForm mutasi={mutasi} />}
          {selectedType === 'pindah_masuk' && <PindahMasukForm wilayahTree={wilayahTree} mutasi={mutasi} />}
          {selectedType === 'pindah_keluar' && <PindahKeluarForm mutasi={mutasi} />}
          {selectedType === 'pindah_rt_rw' && <PindahRTRWForm wilayahTree={wilayahTree} mutasi={mutasi} />}
          {selectedType === 'pisah_kk' && <PisahKKForm wilayahTree={wilayahTree} mutasi={mutasi} />}
        </div>
      </div>
    );
  }

  return (
    <div className="p-2 animate-in slide-in-from-bottom-4 duration-500">
      <div className="mb-8 flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-black text-gray-900 tracking-tight">Pilih Jenis Mutasi</h2>
          <p className="text-sm font-medium text-gray-500 mt-2">Pilih salah satu kategori mutasi di bawah ini untuk memulai proses pendataan.</p>
        </div>
        <Link 
          href={route('mutasi.data.index')}
          className="flex items-center gap-2 px-5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-xl text-xs font-bold transition-all border border-gray-100"
        >
          <ArrowLeft className="w-4 h-4" />
          KEMBALI KE DAFTAR
        </Link>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {MUTASI_TYPES.map((type) => {
          const Icon = type.icon;
          return (
            <button
              key={type.id}
              onClick={() => setSelectedType(type.id)}
              className="group p-6 text-left bg-white border border-gray-100 rounded-[32px] hover:border-blue-500 hover:bg-blue-50/30 transition-all shadow-sm hover:shadow-xl hover:shadow-blue-900/5 flex flex-col items-start gap-5 active:scale-[0.98]"
            >
              <div className={`w-16 h-16 rounded-[24px] flex items-center justify-center border shadow-sm group-hover:scale-110 transition-transform ${
                type.color === 'red' ? 'bg-red-50 text-red-600 border-red-100' :
                type.color === 'blue' ? 'bg-blue-50 text-blue-600 border-blue-100' :
                type.color === 'green' ? 'bg-green-50 text-green-600 border-green-100' :
                type.color === 'orange' ? 'bg-orange-50 text-orange-600 border-orange-100' :
                type.color === 'purple' ? 'bg-purple-50 text-purple-600 border-purple-100' :
                'bg-teal-50 text-teal-600 border-teal-100'
              }`}>
                <Icon className="w-8 h-8" />
              </div>
              <div className="flex-1 w-full">
                <h3 className={`text-lg font-black transition-colors ${
                    type.color === 'red' ? 'group-hover:text-red-600' :
                    type.color === 'blue' ? 'group-hover:text-blue-600' :
                    type.color === 'green' ? 'group-hover:text-green-600' :
                    type.color === 'orange' ? 'group-hover:text-orange-600' :
                    type.color === 'purple' ? 'group-hover:text-purple-600' :
                    'group-hover:text-teal-600'
                }`}>{type.title}</h3>
                <p className="text-sm font-medium text-gray-400 mt-2 leading-relaxed group-hover:text-gray-500 transition-colors">
                  {type.description}
                </p>
                <div className={`mt-6 pt-4 border-t border-gray-50 flex items-center justify-between w-full opacity-0 group-hover:opacity-100 transition-all translate-y-[10px] group-hover:translate-y-0 text-xs font-black uppercase tracking-wider ${
                    type.color === 'red' ? 'text-red-500' :
                    type.color === 'blue' ? 'text-blue-500' :
                    type.color === 'green' ? 'text-green-500' :
                    type.color === 'orange' ? 'text-orange-500' :
                    type.color === 'purple' ? 'text-purple-500' :
                    'text-teal-500'
                }`}>
                  <span>PILIH MUTASI INI</span>
                  <ChevronRight className="w-4 h-4" />
                </div>
              </div>
            </button>
          );
        })}
      </div>
    </div>
  );
}
