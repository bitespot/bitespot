import React, { useState } from 'react';
import { MapPin, Image as ImageIcon, Info, Tag, DollarSign, Plus } from 'lucide-react';

export default function AddSpotPage({ onAddSpot, navigateTo }: { onAddSpot: (spot: any) => void, navigateTo: (page: string) => void }) {
  const [formData, setFormData] = useState({
    name: '',
    category: 'Restaurant',
    priceRange: '$$',
    location: '',
    description: '',
    image: ''
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const newSpot = {
      id: Date.now(),
      ...formData,
      rating: 5.0,
      reviews: 1,
      distance: '0.1 km',
      tags: ['New Discovery'],
      image: formData.image || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80'
    };
    onAddSpot(newSpot);
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  return (
    <div className="bg-gray-50 min-h-[calc(100vh-64px)] py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="bg-charcoal text-white p-6 sm:p-8">
          <h1 className="text-2xl sm:text-3xl font-bold mb-2">Add a Discovered Spot</h1>
          <p className="text-gray-300">Found a hidden gem? Share it with the BiteSpot community!</p>
        </div>
        
        <form onSubmit={handleSubmit} className="p-6 sm:p-8 space-y-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
              <Tag size={16} className="mr-2 text-primary" /> Spot Name
            </label>
            <input required type="text" name="name" value={formData.name} onChange={handleChange} className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all" placeholder="e.g. The Secret Bakery" />
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                <Info size={16} className="mr-2 text-primary" /> Category
              </label>
              <select name="category" value={formData.category} onChange={handleChange} className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all bg-white">
                <option>Restaurant</option>
                <option>Street Food</option>
                <option>Café</option>
                <option>Desserts</option>
                <option>Drinks</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                <DollarSign size={16} className="mr-2 text-primary" /> Price Range
              </label>
              <select name="priceRange" value={formData.priceRange} onChange={handleChange} className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all bg-white">
                <option>$</option>
                <option>$$</option>
                <option>$$$</option>
                <option>$$$$</option>
              </select>
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
              <MapPin size={16} className="mr-2 text-primary" /> Location
            </label>
            <input required type="text" name="location" value={formData.location} onChange={handleChange} className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all" placeholder="e.g. 123 Main St, Downtown" />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
              <ImageIcon size={16} className="mr-2 text-primary" /> Image URL (Optional)
            </label>
            <input type="url" name="image" value={formData.image} onChange={handleChange} className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all" placeholder="https://example.com/image.jpg" />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2 flex items-center">
              <Info size={16} className="mr-2 text-primary" /> Description
            </label>
            <textarea required name="description" value={formData.description} onChange={handleChange} rows={4} className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all resize-none" placeholder="What makes this place special?"></textarea>
          </div>

          <div className="pt-4 flex gap-4">
            <button type="button" onClick={() => navigateTo('home')} className="flex-1 px-6 py-3 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
              Cancel
            </button>
            <button type="submit" className="flex-1 px-6 py-3 rounded-xl font-bold text-white bg-primary hover:bg-primary-hover transition-colors flex items-center justify-center">
              <Plus size={20} className="mr-2" /> Add Spot
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
