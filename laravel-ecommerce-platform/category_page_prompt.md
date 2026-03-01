# Professional Category/Sub-Category Page Frontend Development Prompt

## Overview
Create a fully-featured product category page for a furniture e-commerce website. The page should display products in an organized, filterable grid with sorting capabilities, providing an optimal shopping experience across all devices.

---

## Reference Site Analysis: gumastore.com/tablo

### Key Design Elements Identified

**1. Page Header**
- Breadcrumb navigation (Home > Tablo)
- Category title as H1 (large typography: ~40px on desktop)
- Clean separation from product grid

**2. Product Grid Layout**
- Mobile: 2 columns (`grid-cols-2`)
- Desktop: 3 columns (`lg:grid-cols-3`)
- Product cards with square aspect ratio (`aspect-product`)
- Consistent gaps between cards

**3. Product Card Structure**
- Product image (primary visual)
- Product name (truncated if necessary)
- Price with currency symbol (₺)
- Hover state with subtle effects
- Click navigates to product detail

**4. Interaction Elements**
- Sort dropdown ("Sırala" - Sort)
- Filter button (mobile visible, desktop sidebar)
- Load more / Pagination

---

## Technical Requirements



### Performance Standards
- Lighthouse score: 90+ for all metrics
- First Contentful Paint: < 1.5s
- Largest Contentful Paint: < 2.5s
- Cumulative Layout Shift: < 0.1
- Image lazy loading for below-fold products

---


## Detailed Component Specifications

### 1. CategoryHeader Component

**Structure:**
```tsx
<section className="wrapper mb-6 mt-4">
  {/* Breadcrumb */}
  <nav aria-label="Breadcrumb">
    <ol className="flex items-center gap-2 text-sm text-muted-foreground">
      <li><a href="/">Home</a></li>
      <li>/</li>
      <li className="text-foreground">{categoryName}</li>
    </ol>
  </nav>
  
  {/* Category Title */}
  <h1 className="text-3xl xl:text-[40px] font-medium mt-2">
    {categoryName}
  </h1>
  
  {/* Product Count (optional) */}
  <p className="text-muted-foreground mt-1">
    {totalProducts} products
  </p>
</section>
```

**Features:**
- Schema.org BreadcrumbList markup
- SEO-friendly H1
- Product count display
- Optional category description

---

### 2. FilterSidebar Component (Desktop)

**Structure:**
```tsx
<aside className="w-64 shrink-0 hidden lg:block">
  <div className="sticky top-24 space-y-6">
    {/* Price Range */}
    <FilterSection title="Price">
      <Slider min={0} max={maxPrice} step={1000} />
      <div className="flex justify-between text-sm">
        <span>₺{minPrice}</span>
        <span>₺{maxPrice}</span>
      </div>
    </FilterSection>
    
    {/* Color Filter */}
    <FilterSection title="Color">
      <div className="flex flex-wrap gap-2">
        {colors.map(color => (
          <ColorSwatch key={color} color={color} />
        ))}
      </div>
    </FilterSection>
    
    {/* Material Filter */}
    <FilterSection title="Material">
      {materials.map(material => (
        <Checkbox key={material} label={material} />
      ))}
    </FilterSection>
    
    {/* Availability */}
    <FilterSection title="Availability">
      <Checkbox label="In Stock Only" />
    </FilterSection>
    
    {/* Clear Filters */}
    <Button variant="outline" className="w-full">
      Clear All Filters
    </Button>
  </div>
</aside>
```

**Filter Sections to Include:**
1. **Price Range** - Slider with min/max inputs
2. **Color** - Color swatches or checkboxes
3. **Material** - Checkbox list (Wood, Metal, Fabric, Leather, etc.)
4. **Size/Dimensions** - Dropdown or checkbox
5. **Availability** - In stock toggle
6. **Brand** - Checkbox list (if applicable)
7. **Rating** - Star rating filter (optional)

---

### 3. FilterMobile Component (Drawer)

**Structure:**
```tsx
<Sheet>
  <SheetTrigger asChild>
    <Button variant="outline" className="lg:hidden">
      <Filter className="mr-2 h-4 w-4" />
      Filters
      {activeFilterCount > 0 && (
        <Badge className="ml-2">{activeFilterCount}</Badge>
      )}
    </Button>
  </SheetTrigger>
  
  <SheetContent side="left" className="w-80">
    <SheetHeader>
      <SheetTitle>Filters</SheetTitle>
    </SheetHeader>
    
    {/* Filter sections - same as desktop */}
    <div className="mt-6 space-y-6">
      {/* Price, Color, Material, etc. */}
    </div>
    
    <SheetFooter>
      <Button variant="outline" onClick={clearFilters}>
        Clear All
      </Button>
      <Button onClick={applyFilters}>
        Show {filteredCount} Results
      </Button>
    </SheetFooter>
  </SheetContent>
</Sheet>
```

**Features:**
- Slide-in from left
- Full filter options
- Active filter count badge
- Apply/Clear buttons
- Close on outside click

---

### 4. SortDropdown Component

**Structure:**
```tsx
<Select value={sortBy} onValueChange={setSortBy}>
  <SelectTrigger className="w-48">
    <SelectValue placeholder="Sort by" />
  </SelectTrigger>
  <SelectContent>
    <SelectItem value="newest">Newest</SelectItem>
    <SelectItem value="price-asc">Price: Low to High</SelectItem>
    <SelectItem value="price-desc">Price: High to Low</SelectItem>
    <SelectItem value="name-asc">Name: A to Z</SelectItem>
    <SelectItem value="name-desc">Name: Z to A</SelectItem>
    <SelectItem value="rating">Best Rating</SelectItem>
    <SelectItem value="popular">Most Popular</SelectItem>
  </SelectContent>
</Select>
```

**Sort Options:**
| Value | Label | Description |
|-------|-------|-------------|
| `newest` | Newest | Latest products first |
| `price-asc` | Price: Low to High | Ascending price |
| `price-desc` | Price: High to Low | Descending price |
| `name-asc` | Name: A to Z | Alphabetical |
| `name-desc` | Name: Z to A | Reverse alphabetical |
| `rating` | Best Rating | Highest rated first |
| `popular` | Most Popular | Best sellers first |

---

### 5. ProductGrid Component

**Structure:**
```tsx
<section>
  {/* Toolbar */}
  <div className="flex items-center justify-between mb-6">
    <p className="text-sm text-muted-foreground">
      Showing {start}-{end} of {total} products
    </p>
    
    <div className="flex items-center gap-4">
      {/* Mobile Filter Button */}
      <FilterMobile />
      
      {/* Sort Dropdown */}
      <SortDropdown />
      
      {/* View Toggle (optional) */}
      <ViewToggle view={view} onViewChange={setView} />
    </div>
  </div>
  
  {/* Active Filters Display */}
  {activeFilters.length > 0 && (
    <div className="flex flex-wrap gap-2 mb-4">
      {activeFilters.map(filter => (
        <Badge key={filter.id} variant="secondary">
          {filter.label}
          <button onClick={() => removeFilter(filter.id)}>
            <X className="ml-1 h-3 w-3" />
          </button>
        </Badge>
      ))}
      <button className="text-sm text-muted-foreground">
        Clear all
      </button>
    </div>
  )}
  
  {/* Product Grid */}
  <div className="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
    {products.map(product => (
      <ProductCard key={product.id} product={product} />
    ))}
  </div>
  
  {/* Loading State */}
  {isLoading && (
    <div className="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mt-6">
      {Array.from({ length: 6 }).map((_, i) => (
        <ProductCardSkeleton key={i} />
      ))}
    </div>
  )}
  
  {/* Empty State */}
  {!isLoading && products.length === 0 && (
    <EmptyState 
      title="No products found"
      description="Try adjusting your filters"
      action="Clear Filters"
      onAction={clearFilters}
    />
  )}
  
  {/* Pagination or Load More */}
  <div className="mt-8">
    {paginationType === 'loadMore' ? (
      <LoadMore onClick={loadMore} loading={isLoadingMore} />
    ) : (
      <Pagination 
        currentPage={page}
        totalPages={totalPages}
        onPageChange={setPage}
      />
    )}
  </div>
</section>
```

---

### 6. ProductCard Component

**Structure:**
```tsx
<Link 
  href={`/product/${product.slug}`}
  className="group block"
>
  <article className="relative">
    {/* Image Container */}
    <div className="relative aspect-square overflow-hidden bg-gray-100">
      <Image
        src={product.images[0].url}
        alt={product.name}
        fill
        sizes="(max-width: 768px) 50vw, 33vw"
        className="object-cover transition-transform duration-300 group-hover:scale-105"
        loading="lazy"
      />
      
      {/* Badges */}
      <div className="absolute top-2 left-2 flex flex-col gap-1">
        {product.isNew && (
          <Badge variant="default">New</Badge>
        )}
        {product.discount > 0 && (
          <Badge variant="destructive">-{product.discount}%</Badge>
        )}
      </div>
      
      {/* Quick Actions (on hover) */}
      <div className="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
        <Button size="sm" className="w-full">
          Add to Cart
        </Button>
      </div>
      
      {/* Wishlist Button */}
      <button 
        className="absolute top-2 right-2 p-2 bg-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
        onClick={toggleWishlist}
      >
        <Heart className={isWishlisted ? 'fill-red-500 text-red-500' : ''} />
      </button>
    </div>
    
    {/* Product Info */}
    <div className="mt-3 space-y-1">
      <h3 className="font-medium text-sm line-clamp-2 group-hover:underline">
        {product.name}
      </h3>
      
      {/* Price */}
      <div className="flex items-center gap-2">
        <span className="font-semibold">
          {formatPrice(product.price)}
        </span>
        {product.originalPrice && (
          <span className="text-sm text-muted-foreground line-through">
            {formatPrice(product.originalPrice)}
          </span>
        )}
      </div>
      
      {/* Color Options (optional) */}
      {product.colors && (
        <div className="flex gap-1 mt-2">
          {product.colors.slice(0, 4).map(color => (
            <span 
              key={color}
              className="w-4 h-4 rounded-full border"
              style={{ backgroundColor: color }}
            />
          ))}
          {product.colors.length > 4 && (
            <span className="text-xs text-muted-foreground">
              +{product.colors.length - 4}
            </span>
          )}
        </div>
      )}
      
      {/* Rating (optional) */}
      {product.rating && (
        <div className="flex items-center gap-1">
          <Stars rating={product.rating} />
          <span className="text-xs text-muted-foreground">
            ({product.reviewCount})
          </span>
        </div>
      )}
    </div>
  </article>
</Link>
```

**Card Features:**
- Hover zoom effect on image
- Badges (New, Sale, Out of Stock)
- Quick add to cart button
- Wishlist heart button
- Price with original/discount display
- Color variant indicators
- Rating stars (optional)
- Lazy loading images

---

### 7. Pagination Component

**Structure:**
```tsx
<nav aria-label="Pagination" className="flex justify-center">
  <ul className="flex items-center gap-1">
    {/* Previous */}
    <li>
      <Button
        variant="outline"
        size="icon"
        disabled={currentPage === 1}
        onClick={() => onPageChange(currentPage - 1)}
      >
        <ChevronLeft />
      </Button>
    </li>
    
    {/* Page Numbers */}
    {getPageNumbers().map(page => (
      <li key={page}>
        {page === '...' ? (
          <span className="px-3">...</span>
        ) : (
          <Button
            variant={page === currentPage ? 'default' : 'outline'}
            size="icon"
            onClick={() => onPageChange(page as number)}
          >
            {page}
          </Button>
        )}
      </li>
    ))}
    
    {/* Next */}
    <li>
      <Button
        variant="outline"
        size="icon"
        disabled={currentPage === totalPages}
        onClick={() => onPageChange(currentPage + 1)}
      >
        <ChevronRight />
      </Button>
    </li>
  </ul>
</nav>
```

**Pagination Logic:**
- Show first page, last page, current page ± 2
- Use ellipsis for gaps
- Mobile: Show fewer page numbers
- Include Previous/Next arrows

---

### 8. LoadMore Component

**Structure:**
```tsx
<div className="flex flex-col items-center gap-4">
  <Button 
    variant="outline" 
    size="lg"
    onClick={onLoadMore}
    disabled={loading}
    className="min-w-48"
  >
    {loading ? (
      <>
        <Loader2 className="mr-2 h-4 w-4 animate-spin" />
        Loading...
      </>
    ) : (
      'Load More Products'
    )}
  </Button>
  
  <p className="text-sm text-muted-foreground">
    Showing {currentCount} of {totalCount} products
  </p>
</div>
```

---

### 9. EmptyState Component

**Structure:**
```tsx
<div className="flex flex-col items-center justify-center py-16 text-center">
  <div className="w-24 h-24 mb-4 text-muted-foreground">
    <Search className="w-full h-full" />
  </div>
  <h3 className="text-xl font-medium mb-2">{title}</h3>
  <p className="text-muted-foreground mb-6 max-w-md">
    {description}
  </p>
  {action && (
    <Button onClick={onAction}>{action}</Button>
  )}
</div>
```

---

### 10. ProductCardSkeleton Component

**Structure:**
```tsx
<div className="space-y-3">
  <Skeleton className="aspect-square w-full" />
  <Skeleton className="h-4 w-3/4" />
  <Skeleton className="h-4 w-1/2" />
</div>
```

---

## Data Structures / TypeScript Interfaces

```typescript
// Product interface for category page
interface CategoryProduct {
  id: string;
  name: string;
  slug: string;
  price: number;
  originalPrice?: number;
  discount?: number;
  images: {
    url: string;
    alt: string;
  }[];
  colors?: string[];
  isNew?: boolean;
  inStock: boolean;
  rating?: number;
  reviewCount?: number;
}

// Filter interface
interface Filter {
  id: string;
  type: 'price' | 'color' | 'material' | 'size' | 'availability' | 'brand';
  label: string;
  value: string | number | [number, number];
}

// Sort options
type SortOption = 
  | 'newest' 
  | 'price-asc' 
  | 'price-desc' 
  | 'name-asc' 
  | 'name-desc' 
  | 'rating' 
  | 'popular';

// Category page props
interface CategoryPageProps {
  category: {
    id: string;
    name: string;
    slug: string;
    description?: string;
    image?: string;
    parentCategory?: {
      id: string;
      name: string;
      slug: string;
    };
  };
  products: CategoryProduct[];
  filters: {
    priceRange: [number, number];
    colors: string[];
    materials: string[];
    sizes: string[];
    brands: string[];
  };
  pagination: {
    currentPage: number;
    totalPages: number;
    totalProducts: number;
    productsPerPage: number;
  };
}

// Filter state
interface FilterState {
  priceRange: [number, number];
  selectedColors: string[];
  selectedMaterials: string[];
  selectedSizes: string[];
  selectedBrands: string[];
  inStockOnly: boolean;
  sortBy: SortOption;
}

// URL params for sharing/bookmarking
interface CategoryURLParams {
  page?: number;
  sort?: SortOption;
  minPrice?: number;
  maxPrice?: number;
  colors?: string;
  materials?: string;
  sizes?: string;
  brands?: string;
  inStock?: boolean;
}
```

---

## URL Parameter Management

### Filter URL Strategy

Keep filters in URL for shareability and browser history:

```
/category/tablo?sort=price-asc&minPrice=10000&maxPrice=30000&colors=black,white&page=2
```

**Implementation:**
```typescript
// hooks/useCategoryFilters.ts
import { useSearchParams, useRouter } from 'next/navigation';

export function useCategoryFilters() {
  const searchParams = useSearchParams();
  const router = useRouter();

  const updateFilters = (updates: Record<string, string | null>) => {
    const params = new URLSearchParams(searchParams.toString());
    
    Object.entries(updates).forEach(([key, value]) => {
      if (value === null || value === '') {
        params.delete(key);
      } else {
        params.set(key, value);
      }
    });
    
    router.push(`?${params.toString()}`, { scroll: false });
  };

  return {
    filters: {
      minPrice: searchParams.get('minPrice'),
      maxPrice: searchParams.get('maxPrice'),
      colors: searchParams.get('colors')?.split(','),
      materials: searchParams.get('materials')?.split(','),
      sort: searchParams.get('sort') as SortOption,
      page: parseInt(searchParams.get('page') || '1'),
    },
    updateFilters,
  };
}
```

---

## Responsive Design Specifications

### Breakpoints

```css
/* Mobile First */
sm: 640px   /* Small tablets */
md: 768px   /* Tablets */
lg: 1024px  /* Small laptops - Filter sidebar appears */
xl: 1280px  /* Desktops */
2xl: 1536px /* Large screens */
```

### Layout Behavior

| Breakpoint | Grid Columns | Filter Location | Card Size |
|------------|--------------|-----------------|-----------|
| Mobile (<640px) | 2 columns | Hidden (drawer) | ~50vw - gap |
| Tablet (640-1024px) | 2-3 columns | Hidden (drawer) | ~33vw - gap |
| Desktop (>1024px) | 3-4 columns | Left sidebar | ~25-33vw |

### Grid Classes

```css
/* Product grid responsive */
.product-grid {
  @apply grid gap-4 md:gap-6;
  @apply grid-cols-2;      /* Mobile: 2 columns */
  @apply md:grid-cols-2;   /* Tablet: 2 columns */
  @apply lg:grid-cols-3;   /* Desktop: 3 columns */
  @apply xl:grid-cols-4;   /* Large: 4 columns (optional) */
}
```

---

## Accessibility Requirements

### WCAG 2.1 AA Compliance

1. **Keyboard Navigation**
   - All filter controls accessible via Tab
   - Enter/Space to toggle filters
   - Escape to close mobile filter drawer

2. **Screen Readers**
   - Proper ARIA labels on all interactive elements
   - Announce filter changes
   - Announce loaded products count
   - Landmark regions for filter sidebar

3. **Visual**
   - Focus indicators on all focusable elements
   - Minimum contrast ratio 4.5:1
   - Don't rely on color alone for filter status

4. **Motion**
   - Respect `prefers-reduced-motion`
   - Disable hover animations for users who prefer reduced motion

### ARIA Implementation

```tsx
{/* Filter sidebar */}
<aside aria-label="Product filters" role="region">
  <fieldset>
    <legend>Price Range</legend>
    {/* Price slider */}
  </fieldset>
</aside>

{/* Product grid */}
<main aria-label="Products" role="region">
  <ul role="list" aria-label="Product list">
    {products.map(product => (
      <li key={product.id}>
        <article aria-label={product.name}>
          {/* Product card content */}
        </article>
      </li>
    ))}
  </ul>
</main>

{/* Sort dropdown */}
<select aria-label="Sort products by">
  {/* Options */}
</select>

{/* Pagination */}
<nav aria-label="Pagination">
  <ul>
    {/* Page buttons */}
  </ul>
</nav>
```

---

## Performance Optimizations

### 1. Image Optimization
```tsx
<Image
  src={product.images[0].url}
  alt={product.name}
  fill
  sizes="(max-width: 768px) 50vw, (max-width: 1024px) 33vw, 25vw"
  loading="lazy"
  placeholder="blur"
  blurDataURL={product.images[0].blurDataURL}
/>
```

### 2. Virtual Scrolling (for large catalogs)
Consider virtual scrolling for categories with 100+ products:
```tsx
import { VirtualGrid } from '@tanstack/react-virtual';
```

### 3. Debounced Filter Updates
```tsx
import { useDebouncedCallback } from 'use-debounce';

const debouncedPriceFilter = useDebouncedCallback((value) => {
  updateFilters({ minPrice: value[0], maxPrice: value[1] });
}, 300);
```

### 4. Infinite Scroll Alternative
```tsx
import { useInView } from 'react-intersection-observer';

const { ref, inView } = useInView({
  threshold: 0,
});

useEffect(() => {
  if (inView && hasMore && !isLoading) {
    loadMore();
  }
}, [inView, hasMore, isLoading]);
```

### 5. Prefetch Product Pages
```tsx
import { useRouter } from 'next/router';

const prefetchProduct = (slug: string) => {
  router.prefetch(`/product/${slug}`);
};

// On card hover
<Link 
  href={`/product/${product.slug}`}
  onMouseEnter={() => prefetchProduct(product.slug)}
>
```

---

## SEO Requirements

### 1. Meta Tags
```tsx
<Head>
  <title>{category.name} | Store Name</title>
  <meta name="description" content={category.description} />
  <meta property="og:title" content={category.name} />
  <meta property="og:description" content={category.description} />
  <meta property="og:image" content={category.image} />
  <link rel="canonical" href={`https://store.com/${category.slug}`} />
</Head>
```

### 2. Schema.org Markup
```tsx
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "{category.name}",
  "description": "{category.description}",
  "url": "https://store.com/{category.slug}",
  "mainEntity": {
    "@type": "ItemList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "item": {
          "@type": "Product",
          "name": "{product.name}",
          "image": "{product.image}",
          "offers": {
            "@type": "Offer",
            "price": "{product.price}",
            "priceCurrency": "TRY"
          }
        }
      }
      // ... more products
    ]
  }
}
</script>
```

### 3. Pagination SEO
```tsx
{/* For paginated results */}
<link rel="prev" href="?page=1" />
<link rel="next" href="?page=3" />
```

---

## Animation Guidelines

### Card Hover Effects
```css
.product-card {
  @apply transition-all duration-300 ease-out;
}

.product-card:hover {
  @apply shadow-lg;
}

.product-card:hover .product-image {
  @apply scale-105;
}

.product-card .product-image {
  @apply transition-transform duration-500 ease-out;
}
```

### Filter Transitions
```css
.filter-section {
  @apply transition-all duration-200 ease-in-out;
}

.filter-section.collapsed {
  @apply h-0 overflow-hidden opacity-0;
}

.filter-section.expanded {
  @apply h-auto opacity-100;
}
```

### Mobile Drawer
```tsx
<SheetContent 
  className="transition-transform duration-300 ease-in-out"
>
```

---

## Error Handling

### Error States
1. **No Products** - Empty state with clear filters CTA
2. **API Error** - Retry button with error message
3. **Network Error** - Offline indicator with retry

```tsx
{error && (
  <div className="flex flex-col items-center py-16">
    <AlertCircle className="w-16 h-16 text-destructive mb-4" />
    <h3 className="text-xl font-medium mb-2">Something went wrong</h3>
    <p className="text-muted-foreground mb-4">{error.message}</p>
    <Button onClick={retry}>Try Again</Button>
  </div>
)}
```

---

## Complete Page Layout

```tsx
// app/[category]/page.tsx
export default function CategoryPage({ params, searchParams }: CategoryPageProps) {
  const { products, filters, pagination, category } = useCategoryData(params.category, searchParams);

  return (
    <>
      <Head>
        {/* SEO meta tags */}
      </Head>
      
      <main className="min-h-screen">
        {/* Header */}
        <CategoryHeader 
          category={category}
          totalProducts={pagination.totalProducts}
        />
        
        <div className="wrapper">
          <div className="flex gap-8">
            {/* Desktop Filter Sidebar */}
            <FilterSidebar 
              filters={filters}
              className="hidden lg:block"
            />
            
            {/* Main Content */}
            <div className="flex-1">
              {/* Toolbar */}
              <CategoryToolbar 
                sortBy={searchParams.sort}
                activeFilters={activeFilters}
              />
              
              {/* Product Grid */}
              <ProductGrid 
                products={products}
                isLoading={isLoading}
              />
              
              {/* Pagination */}
              <Pagination 
                currentPage={pagination.currentPage}
                totalPages={pagination.totalPages}
              />
            </div>
          </div>
        </div>
      </main>
    </>
  );
}
```

---

## Quality Checklist

Before finalizing, verify:

- [ ] All components render without errors
- [ ] Responsive design works on all breakpoints
- [ ] Filter state persists in URL
- [ ] Sort functionality works correctly
- [ ] Pagination/load more functions
- [ ] Empty state displays when no products
- [ ] Loading skeletons display during fetch
- [ ] Images lazy load properly
- [ ] Keyboard navigation is complete
- [ ] Screen reader testing passed
- [ ] Performance metrics meet targets
- [ ] No console errors or warnings
- [ ] Schema.org markup validates
- [ ] Meta tags are correct
- [ ] Animations are smooth (60fps)
- [ ] Mobile filter drawer opens/closes smoothly
- [ ] Active filters display and can be removed
- [ ] Product cards link to correct detail pages
