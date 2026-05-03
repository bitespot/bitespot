import React, { useState } from 'react';
import { LayoutDashboard, UtensilsCrossed, Star, TrendingUp, Image as ImageIcon, Settings, Plus, Edit2, Trash2, MoreVertical } from 'lucide-react';

export default function VendorDashboard({ navigateTo }) {
  const [activeTab, setActiveTab] = useState('overview');

  const stats = [
    { label: 'Total Views', value: '12.4K', change: '+14%', icon: <TrendingUp size={20} className="text-blue-500" /> },
    { label: 'Average Rating', value: '4.8', change: '+0.2', icon: <Star size={20} className="text-yellow-500" /> },
    { label: 'Menu Items', value: '24', change: '+2', icon: <UtensilsCrossed size={20} className="text-green-500" /> },
  ];

  const menuItems = [
    { id: 1, name: 'Signature Dish', category: 'Mains', price: '$18.50', status: 'Active', image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=100&q=80' },
    { id: 2, name: 'Spicy Noodles', category: 'Mains', price: '$14.00', status: 'Active', image: 'https://images.unsplash.com/photo-1552611052-33e04de081de?auto=format&fit=crop&w=100&q=80' },
    { id: 3, name: 'Classic Burger', category: 'Mains', price: '$16.00', status: 'Sold Out', image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=100&q=80' },
    { id: 4, name: 'Fresh Salad', category: 'Starters', price: '$12.00', status: 'Active', image: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=100&q=80' },
  ];

  return (
    <div className="flex flex-col md:flex-row min-h-[calc(100vh-64px)] bg-gray-50">
      {/* Sidebar */}
      <div className="w-full md:w-64 bg-white border-r border-gray-200 flex-shrink-0">
        <div className="p-6">
          <div className="flex items-center gap-3 mb-8">
            <div className="w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center font-bold text-xl">
              RS
            </div>
            <div>
              <h2 className="font-bold text-charcoal">The Rustic Spoon</h2>
              <p className="text-xs text-gray-500">Vendor Account</p>
            </div>
          </div>

          <nav className="space-y-1">
            <button 
              onClick={() => setActiveTab('overview')}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${
                activeTab === 'overview' ? 'bg-cream text-primary' : 'text-gray-600 hover:bg-gray-50'
              }`}
            >
              <LayoutDashboard size={18} /> Overview
            </button>
            <button 
              onClick={() => setActiveTab('menu')}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${
                activeTab === 'menu' ? 'bg-cream text-primary' : 'text-gray-600 hover:bg-gray-50'
              }`}
            >
              <UtensilsCrossed size={18} /> Menu Management
            </button>
            <button 
              onClick={() => setActiveTab('reviews')}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${
                activeTab === 'reviews' ? 'bg-cream text-primary' : 'text-gray-600 hover:bg-gray-50'
              }`}
            >
              <Star size={18} /> Reviews
            </button>
            <button 
              onClick={() => setActiveTab('photos')}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${
                activeTab === 'photos' ? 'bg-cream text-primary' : 'text-gray-600 hover:bg-gray-50'
              }`}
            >
              <ImageIcon size={18} /> Photos & Promos
            </button>
            <button 
              onClick={() => setActiveTab('settings')}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${
                activeTab === 'settings' ? 'bg-cream text-primary' : 'text-gray-600 hover:bg-gray-50'
              }`}
            >
              <Settings size={18} /> Settings
            </button>
          </nav>
        </div>
      </div>

      {/* Main Content */}
      <div className="flex-1 p-6 md:p-8 overflow-y-auto">
        <div className="max-w-5xl mx-auto">
          <div className="flex justify-between items-center mb-8">
            <h1 className="text-2xl font-bold text-charcoal capitalize">{activeTab.replace('-', ' ')}</h1>
            {activeTab === 'menu' && (
              <button className="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-lg font-medium flex items-center transition-colors">
                <Plus size={18} className="mr-2" /> Add Item
              </button>
            )}
          </div>

          {activeTab === 'overview' && (
            <>
              {/* Stats Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                {stats.map((stat, index) => (
                  <div key={index} className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div className="flex justify-between items-start mb-4">
                      <div className="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                        {stat.icon}
                      </div>
                      <span className="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">
                        {stat.change}
                      </span>
                    </div>
                    <h3 className="text-gray-500 text-sm font-medium mb-1">{stat.label}</h3>
                    <p className="text-3xl font-bold text-charcoal">{stat.value}</p>
                  </div>
                ))}
              </div>

              {/* Recent Activity */}
              <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 className="text-lg font-bold text-charcoal mb-4">Recent Reviews</h2>
                <div className="space-y-4">
                  {[1, 2].map((i) => (
                    <div key={i} className="flex gap-4 p-4 border border-gray-100 rounded-xl">
                      <div className="w-10 h-10 rounded-full bg-gray-200 flex-shrink-0 overflow-hidden">
                        <img src={`https://i.pravatar.cc/150?img=${i + 10}`} alt="User" className="w-full h-full object-cover" />
                      </div>
                      <div>
                        <div className="flex items-center gap-2 mb-1">
                          <h4 className="font-bold text-charcoal text-sm">Customer {i}</h4>
                          <div className="flex">
                            {[...Array(5)].map((_, j) => (
                              <Star key={j} size={12} className={j < 4 ? "text-yellow-400 fill-yellow-400" : "text-gray-300"} />
                            ))}
                          </div>
                          <span className="text-xs text-gray-400 ml-auto">2 hours ago</span>
                        </div>
                        <p className="text-sm text-gray-600">Great food and amazing service! The new spicy noodles are a must-try.</p>
                        <button className="text-primary text-xs font-medium mt-2 hover:underline">Reply to review</button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </>
          )}

          {activeTab === 'menu' && (
            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-gray-50 border-b border-gray-100">
                      <th className="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                      <th className="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                      <th className="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Price</th>
                      <th className="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                      <th className="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100">
                    {menuItems.map((item) => (
                      <tr key={item.id} className="hover:bg-gray-50 transition-colors">
                        <td className="p-4">
                          <div className="flex items-center gap-3">
                            <img src={item.image} alt={item.name} className="w-12 h-12 rounded-lg object-cover" />
                            <span className="font-medium text-charcoal">{item.name}</span>
                          </div>
                        </td>
                        <td className="p-4 text-sm text-gray-600">{item.category}</td>
                        <td className="p-4 text-sm font-medium text-charcoal">{item.price}</td>
                        <td className="p-4">
                          <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                            item.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                          }`}>
                            {item.status}
                          </span>
                        </td>
                        <td className="p-4 text-right">
                          <div className="flex justify-end gap-2 text-gray-400">
                            <button className="p-1 hover:text-primary transition-colors"><Edit2 size={16} /></button>
                            <button className="p-1 hover:text-red-500 transition-colors"><Trash2 size={16} /></button>
                            <button className="p-1 hover:text-gray-600 transition-colors"><MoreVertical size={16} /></button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}
          
          {/* Placeholder for other tabs */}
          {['reviews', 'photos', 'settings'].includes(activeTab) && (
            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
              <div className="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                {activeTab === 'reviews' && <Star size={24} />}
                {activeTab === 'photos' && <ImageIcon size={24} />}
                {activeTab === 'settings' && <Settings size={24} />}
              </div>
              <h3 className="text-lg font-bold text-charcoal mb-2 capitalize">{activeTab} Management</h3>
              <p className="text-gray-500">This section is currently under development.</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
